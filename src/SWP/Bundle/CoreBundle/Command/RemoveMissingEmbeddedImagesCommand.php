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

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class RemoveMissingEmbeddedImagesCommand extends Command
{
    protected static $defaultName = 'swp:fixer:remove-missing-embedded-images';

    private $entityManager;

    private $articleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ArticleRepositoryInterface $articleRepository
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->articleRepository = $articleRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Remove from article body missing embedded images')
            ->addOption('dry-run', null, InputArgument::OPTIONAL, 'Do not execute anything, just show what was found', false)
            ->addArgument('term', InputArgument::REQUIRED, 'Article body fragment')
            ->addArgument('parent', InputArgument::REQUIRED, 'Fount element parent to be removed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var ArticleInterface[] $brokenArticles */
        $brokenArticles = $this->articleRepository->getArticlesByBodyContent($input->getArgument('term'));
        foreach ($brokenArticles as $article) {
            $articleBody = $article->getBody();
            $crawler = new Crawler($articleBody);
            $crawler->filter('img')
                ->reduce(function (Crawler $node, $i) use ($input) {
                    if (false === strpos($node->attr('src'), $input->getArgument('term'))) {
                        return false;
                    }
                })
                ->each(static function (Crawler $crawler, $i) use ($input) {
                    foreach ($crawler->closest($input->getArgument('parent')) as $node) {
                        $node->parentNode->removeChild($node);
                    }
                });

            $newContent = $crawler->filter('html body')->each(static function (Crawler $crawler) {
                return $crawler->html();
            });

            $article->setBody(implode('', $newContent));
        }

        if (true !== $input->getOption('dry-run')) {
            $this->entityManager->flush();
        }

        $output->writeln('<bg=green;options=bold>Done. In total processed '.\count($brokenArticles).' articles.</>');
    }
}
