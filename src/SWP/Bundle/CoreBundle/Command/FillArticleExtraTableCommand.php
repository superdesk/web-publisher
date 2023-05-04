<?php

namespace SWP\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleExtraEmbedField;
use SWP\Bundle\ContentBundle\Model\ArticleExtraTextField;
use SWP\Bundle\CoreBundle\Model\Article;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillArticleExtraTableCommand extends Command
{
    protected static $defaultName = 'swp:migration:fill-article-extra';

    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Finds articles by term and runs articles body processors on it.')
            ->addOption('limit', null, InputArgument::OPTIONAL, 'Limit.', 2000)
            ->setHelp(<<<'EOT'
The <info>swp:migration:fill-article-extra</info> command will populate <info>swp_article_extra</info> table with data from <info>swp_article.extra</info> field.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $batchSize = 500;
        $limit = $input->getOption('limit');

        $totalArticles = $this->entityManager
            ->createQuery('SELECT count(a) FROM SWP\Bundle\CoreBundle\Model\Article a')
            ->getSingleScalarResult();

        $totalArticlesProcessed = 0;
        $isProcessing = true;

        while ($isProcessing) {
            $sql = "SELECT id, extra FROM swp_article LIMIT $limit OFFSET $totalArticlesProcessed";
            $query = $this->entityManager->getConnection()->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();

            foreach ($results as $result) {
                $legacyExtra = unserialize($result['extra']);
                if (empty($legacyExtra)) {
                    ++$totalArticlesProcessed;
                    continue;
                }

                $article = $this->entityManager->find(
                    Article::class,
                    $result['id']
                );

                foreach ($legacyExtra as $key => $extraItem) {
                    if (is_array($extraItem)) {
                        $extra = ArticleExtraEmbedField::newFromValue($key, $extraItem);
                    } else {
                        $extra = ArticleExtraTextField::newFromValue($key, (string)$extraItem);
                    }
                    $extra->setArticle($article);
                }

                $this->entityManager->persist($extra);

                if (0 === ($totalArticlesProcessed % $batchSize)) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
                ++$totalArticlesProcessed;
            }

            if ($totalArticlesProcessed >= $totalArticles) {
                $isProcessing = false;
                break;
            }

            $this->entityManager->flush();
        }

        return 0;
    }
}