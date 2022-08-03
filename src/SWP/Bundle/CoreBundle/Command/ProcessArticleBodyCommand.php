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
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessorChain;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProcessArticleBodyCommand extends Command
{
    protected static $defaultName = 'swp:article:process:body';

    /** @var RepositoryManagerInterface  */
    private $repositoryManager;

    /** @var TenantContextInterface  */
    private $tenantContext;

    /** @var ParameterBagInterface  */
    private $parameterBag;

    /** @var ArticleRepositoryInterface  */
    private $articleRepository;

    /** @var ArticleBodyProcessorChain  */
    private $articleBodyProcessorChain;

    public function __construct(RepositoryManagerInterface $repositoryManager,
                                TenantContextInterface $tenantContext,
                                ParameterBagInterface $parameterBag,
                                ArticleRepositoryInterface $articleRepository,
                                ArticleBodyProcessorChain $articleBodyProcessorChain )
    {
        $this->repositoryManager = $repositoryManager;
        $this->tenantContext = $tenantContext;
        $this->parameterBag = $parameterBag;
        $this->articleRepository = $articleRepository;
        $this->articleBodyProcessorChain  = $articleBodyProcessorChain;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $term = $input->getArgument('term');

        $currentTenant = $this->tenantContext->getTenant();

        $criteria = Criteria::fromQueryParameters(
            $term,
            [
                'sort' => ['publishedAt' => 'desc'],
                'tenantCode' => $currentTenant->getCode(),
            ]
        );


        $repository = $this->repositoryManager->getRepository($this->parameterBag->get('swp.model.article.class'));
        $articles = $repository
            ->findByCriteria($criteria)
            ->getResults((int) $input->getOption('offset'), (int) $input->getOption('limit'));

        $output->writeln('<bg=green;options=bold>There are total of '.$articles->getTotalHits().' articles.</>');

        $articleBodyProcessorChain = $this->articleBodyProcessorChain;
        $articleRepository = $this->articleRepository;

        foreach ($articles->toArray() as $article) {
            foreach ($article->getMedia() as $media) {
                $articleBodyProcessorChain->process($article, $media);
            }
        }

        $articleRepository->flush();

        $output->writeln('<bg=green;options=bold>Done. Processed '.\count($articles->toArray()).' articles.</>');

        return 0;
    }
}
