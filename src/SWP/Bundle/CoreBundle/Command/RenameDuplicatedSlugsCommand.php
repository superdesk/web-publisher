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
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\Common\Generator\RandomStringGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenameDuplicatedSlugsCommand extends Command
{
    protected static $defaultName = 'swp:article:rename-duplicates';

    private $entityManager;

    private $stringGenerator;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->stringGenerator = new RandomStringGenerator();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Finds articles with duplicated slugs and adds suffixes to them.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $query = $this->entityManager->createQuery('
            SELECT 
                   a.slug, a.deletedAt, COUNT(a.id) 
            FROM 
                 SWP\Bundle\CoreBundle\Model\Article a 
            GROUP BY 
                     a.slug, a.tenantCode, a.organization, a.deletedAt 
            HAVING 
                   COUNT(a.id) > 1
       ');

        $duplicates = $query->getResult();

        foreach ($duplicates as $duplicate) {
            $duplicatedArticles = $this->entityManager->getRepository(Article::class)->findBy(['slug' => $duplicate['slug']]);
            /**
             * @var int
             * @var ArticleInterface $article */
            foreach ($duplicatedArticles as $key => $article) {
                if (0 === $key) {
                    continue;
                }
                $article->setSlug($article->getSlug().'-'.$this->stringGenerator->generate(8));
            }
        }
        $this->entityManager->flush();

        $output->writeln('<bg=green;options=bold>Done. Processed '.\count($duplicates).' articles.</>');
    }
}
