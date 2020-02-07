<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMigrationMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use function explode;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Bundle\CoreBundle\Repository\PackageRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ProcessPackagesCommand extends Command
{
    protected static $defaultName = 'swp:package:process';

    private $eventDispatcher;

    private $tenantContext;

    private $packageRepository;

    private $paginator;

    private $requestStack;

    private $messageBus;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TenantContextInterface $tenantContext,
        PackageRepositoryInterface $packageRepository,
        PaginatorInterface $paginator,
        RequestStack $requestStack,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();

        $this->eventDispatcher = $eventDispatcher;
        $this->tenantContext = $tenantContext;
        $this->packageRepository = $packageRepository;
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Finds packages by term and process them.')
            ->addArgument('article-body-content', InputArgument::OPTIONAL, 'Search for term in package articles body.')
            ->addOption('limit', null, InputArgument::OPTIONAL, 'Pagination limit', 10)
            ->addOption('page', null, InputArgument::OPTIONAL, 'Pagination page', 1)
            ->addOption('order', null, InputArgument::OPTIONAL, 'Packages order. Example: updatedAt=desc', 'updatedAt=desc')
            ->addOption('authors', null, InputArgument::OPTIONAL, 'Filter by authors. Example: "author1,author2"', null)
            ->addOption('statuses', null, InputArgument::OPTIONAL, 'Filter by package statuses. Example: "new,published,unpublished"', null)
            ->addOption('dry-run', null, InputArgument::OPTIONAL, 'Do not execute anything, just show what was found', false)
            ->setHelp(<<<'EOT'
The <info>swp:package:packages</info> finds packages by given term and process them.

  <info>php %command.full_name%</info>

  <info>term</info> argument is the value of the string by which to find the packages.

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $currentTenant = $this->tenantContext->getTenant();
        $this->requestStack->push(new Request());

        $order = [];
        parse_str($input->getOption('order'), $order);

        $filters = [
            'organization' => $currentTenant->getOrganization()->getId(),
        ];
        if (null !== $input->getOption('authors')) {
            $filters['authors'] = explode(',', $input->getOption('authors'));
        }
        if (null !== $input->getOption('statuses')) {
            $filters['statuses'] = explode(',', $input->getOption('statuses'));
        }

        $criteria = new Criteria($filters);

        if (null !== ($term = $input->getArgument('article-body-content'))) {
            $criteria->set('article-body-content', $term);
        }

        $queryBuilder = $this->packageRepository->getQueryByCriteria($criteria, $order, 'p');
        $this->packageRepository->applyCriteria($queryBuilder, $criteria, 'p');

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $queryBuilder,
            $input->getOption('page'),
            $input->getOption('limit')
        );

        $output->writeln(sprintf('<bg=green;options=bold>Packages found: %s</>', $pagination->getTotalItemCount()));

        /** @var PackageInterface $package */
        foreach ($pagination->getItems() as $package) {
            $output->writeln(sprintf('Processing package with guid: %s', $package->getGuid()));
            if (true === (bool) $input->getOption('dry-run')) {
                continue;
            }

            $this->messageBus->disptach(new ContentPushMigrationMessage(
                $currentTenant->getTenant()->getId(),
                $package->getId()
            ));
        }
    }
}
