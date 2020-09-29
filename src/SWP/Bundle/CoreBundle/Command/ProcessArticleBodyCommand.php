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

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessArticleBodyCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'swp:article:process:body';

    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Finds articles by term and runs articles body processors on it.')
            ->addArgument('term', InputArgument::REQUIRED, 'Search term.')
            ->addOption('limit', null, InputArgument::OPTIONAL, 'Limit.', 10)
            ->addOption('offset', null, InputArgument::OPTIONAL, 'Offset.', 0)
            ->setHelp(<<<'EOT'
The <info>swp:article:process</info> finds articles by given term and runs article's body processors on it.

  <info>php %command.full_name% term embedded_image</info>

  <info>term</info> argument is the value of the string by which to find the articles.

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $term = $input->getArgument('term');

        $currentTenant = $this->getContainer()->get('swp_multi_tenancy.tenant_context')->getTenant();

        $criteria = Criteria::fromQueryParameters(
            $term,
            [
                'sort' => ['publishedAt' => 'desc'],
                'tenantCode' => $currentTenant->getCode(),
            ]
        );

        $repository = $this->repositoryManager->getRepository($this->getContainer()->getParameter('swp.model.article.class'));
        $articles = $repository
            ->findByCriteria($criteria, [], true)
            ->getResults((int) $input->getOption('offset'), (int) $input->getOption('limit'));

        $output->writeln('<bg=green;options=bold>There are total of '.$articles->getTotalHits().' articles.</>');

        $articleBodyProcessorChain = $this->getContainer()->get('swp_content_bundle.processor.article_body');
        $articleRepository = $this->getContainer()->get('swp.repository.article');

        foreach ($articles->toArray() as $article) {
            foreach ($article->getMedia() as $media) {
                $articleBodyProcessorChain->process($article, $media);
            }
        }

        $articleRepository->flush();

        $output->writeln('<bg=green;options=bold>Done. Processed '.\count($articles->toArray()).' articles.</>');
    }
}
