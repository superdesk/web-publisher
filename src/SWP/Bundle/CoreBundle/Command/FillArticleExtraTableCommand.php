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

        ini_set('memory_limit', -1);

        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $batchSize = 500;
        $limit = $input->getOption('limit');

        $sql = "select count(*) from swp_article  where extra not like  'a:0%'";
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->execute();
        $articlesWithExtra = $query->fetchOne();

        $sql = "SELECT COUNT(id) FROM swp_article";
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->execute();
        $totalArticles = $query->fetchOne();

        $sql = "SELECT COUNT(id) FROM swp_article_extra";
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->execute();
        $totalArticlesExtra = $query->fetchOne();

        $truncate = true;
        if ($totalArticlesExtra > 0) {
            echo "Article extra table is not empty\n";
            $options = "Article extra table is not empty, select option:\n";
            $options .= "- Truncate [1]\n";
            $options .= "- Overwrite [2]\n";
            $options .= "- Exit [3]\n";
            $res = (int)readline($options);
            if ($res > 2) {
                exit(1);
            }
            $truncate = $res === 1;
        }

        if ($truncate) {
            $this->truncateExtra();
        }

        $totalArticlesProcessed = 0;
        $isProcessing = true;

        echo "Info: " . $totalArticles . " articles will be processed\n" ;
        sleep(1);
        $startTime = microtime(true);
        $iterations = $emptyExtra = $invalidExtra = 0;
        $processed = $duplicates = [];
        while ($isProcessing) {
            $iterations++;
            echo "\033[1A\033[K";
            echo "==> Processed: " . $totalArticlesProcessed . "/" . $totalArticles . " articles in " .  (microtime(true) - $startTime) . "\n";
            $sql = "SELECT id, extra FROM swp_article order by id ASC  OFFSET (($iterations -1) * $limit) LIMIT $limit";
            $query = $this->entityManager->getConnection()->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            if (empty($results)) {
                $isProcessing = false;
            }

            foreach ($results as $result) {
                /**
                 * Here we should do checkup
                 * user $result['id'] to find all article_extra for this id and remove them
                 * This way all new article extra fields that are not from migrated data will be preserved
                 */
                if (in_array($result['extra'], ['a:0%', 'N;'])) {
                    ++$emptyExtra;
                    ++$totalArticlesProcessed;
                    continue;
                }
                $legacyExtra = unserialize($result['extra']);
                if (empty($legacyExtra)) {
                    ++$emptyExtra;
                    ++$totalArticlesProcessed;
                    continue;
                }

                if (!$truncate) {
                    $this->deletePreviousExtra($result['id']);
                }

                $article = $this->entityManager->find(
                    Article::class,
                    $result['id']
                );

                foreach ($legacyExtra as $key => $extraItem) {
                    if (isset($processed[$result['id']][$key])) {
                        $duplicates[] = [
                            'id' => $result['id'],
                            'key' => $key,
                            'value' => $extraItem
                        ];
                        continue;
                    } else {
                        $processed[$result['id']][$key] = true;
                    }
                    if (is_array($extraItem)) {
                        $extra = ArticleExtraEmbedField::newFromValue($key, $extraItem);
                    } else {
                        $extra = ArticleExtraTextField::newFromValue($key, (string)$extraItem);
                    }
                    $extra->setArticle($article);
                }

                $this->entityManager->persist($extra);
                ++$totalArticlesProcessed;

                if (0 === ($totalArticlesProcessed % $batchSize)) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }

            if ($totalArticlesProcessed >= $totalArticles) {
                $isProcessing = false;
            }

        }

        echo "\n...almost finished, just to check if there is something to flush\n";

        $unitOfWork = $this->entityManager->getUnitOfWork();

        $insertions = $unitOfWork->getScheduledEntityInsertions();
        $updates = $unitOfWork->getScheduledEntityUpdates();
        $deletions = $unitOfWork->getScheduledEntityDeletions();

        if (!empty($insertions) || !empty($updates) || !empty($deletions)) {
            // Flush is needed, and you can check the count of changes
            $insertCount = count($insertions);
            $updateCount = count($updates);
            $deleteCount = count($deletions);

            echo "Flush is needed. Insertions: $insertCount, Updates: $updateCount, Deletions: $deleteCount\n";
            $this->entityManager->flush();
            $this->entityManager->clear();
        } else {
            echo "Nothing to flush...\n";
        }

        echo"\n";
        echo "================= DONE =================\n";
        echo "\t- Articles count: $totalArticles\n";
        echo "\t- Processed: $totalArticlesProcessed\n";
        echo "\t- Empty extra: $emptyExtra\n";
        echo "\t- Invalid extra: $invalidExtra\n";
        echo "\t- Iterations: $iterations\n";
        echo "\t- Duplicates count: " . count($duplicates) . "\n";
        echo "=================------==================\n";

        $finalResult = [
            'articles_with_valid_extra' => $articlesWithExtra,
            'total_articles' => $totalArticles,
            'empty_extra' => $emptyExtra,
            'invalid_extra' => $invalidExtra,
            'duplicates' => $duplicates
        ];
        $fileName = '/tmp/fill-article-extra.json';
        file_put_contents($fileName, PHP_EOL . json_encode($finalResult), FILE_APPEND);

        echo ">> See more results in " . $fileName . " file";
        exit(1);
    }

    protected function truncateExtra()
    {
        $this->entityManager->flush();
        $sql = "TRUNCATE swp_article_extra RESTART IDENTITY";
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->execute();
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    protected function deletePreviousExtra(int $id)
    {
        $this->entityManager->flush();
        $sql = "DELETE FROM swp_article_extra sae WHERE sae.article_id=" . $id;
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->execute();
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}