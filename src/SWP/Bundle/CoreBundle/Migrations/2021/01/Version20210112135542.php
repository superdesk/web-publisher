<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use SWP\Bundle\ContentBundle\Model\ArticleExtraEmbedField;
use SWP\Bundle\ContentBundle\Model\ArticleExtraTextField;
use SWP\Bundle\CoreBundle\Model\Article;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210112135542 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('CREATE SEQUENCE IF NOT EXISTS swp_article_extra_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS swp_article_extra (id INT NOT NULL, article_id INT DEFAULT NULL, field_name VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, embed VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_9E61B3177294869C ON swp_article_extra (article_id)');
        $this->addSql(
            'ALTER TABLE swp_article_extra DROP CONSTRAINT IF EXISTS FK_9E61B3177294869C'
        );
        $this->addSql(
            'ALTER TABLE swp_article_extra ADD CONSTRAINT FK_9E61B3177294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('DROP SEQUENCE swp_article_extra_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_extra');
    }
    public function postUp(Schema $schema): void
    {
        try {
            $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
            $connection = $entityManager->getConnection();
            $connection->getConfiguration()->setSQLLogger(null);

            // Verify table exists
            $tableExists = $connection->executeQuery(
                "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'swp_article_extra')"
            )->fetchOne();

            if (!$tableExists) {
                return;
            }

            $batchSize = 500;
            $numberOfRecordsPerPage = 2000;

            // Get total count
            $totalArticles = $entityManager
                ->createQuery('SELECT count(a) FROM SWP\Bundle\CoreBundle\Model\Article a WHERE a.extra IS NOT NULL')
                ->getSingleScalarResult();

            if ($totalArticles == 0) {
                return;
            }

            $totalArticlesProcessed = 0;

            // Pagination loop
            while ($totalArticlesProcessed < $totalArticles) {
                $sql = "SELECT id, extra FROM swp_article WHERE extra IS NOT NULL ORDER BY id LIMIT ? OFFSET ?";
                $query = $connection->prepare($sql);
                $results = $query->executeQuery([$numberOfRecordsPerPage, $totalArticlesProcessed])->fetchAllAssociative();

                // Break if no results (end of data)
                if (empty($results)) {
                    break;
                }

                foreach ($results as $result) {
                    try {
                        $legacyExtra = $this->unserializeExtraField($result['extra']);
                        if (empty($legacyExtra)) {
                            ++$totalArticlesProcessed;
                            continue;
                        }

                        $article = $entityManager->find(
                            Article::class,
                            $result['id']
                        );

                        if (!$article) {
                            ++$totalArticlesProcessed;
                            continue;
                        }

                        foreach ($legacyExtra as $key => $extraItem) {
                            if (is_array($extraItem)) {
                                $extra = ArticleExtraEmbedField::newFromValue($key, $extraItem);
                            } else {
                                $extra = ArticleExtraTextField::newFromValue($key, (string) $extraItem);
                            }
                            $extra->setArticle($article);
                            $entityManager->persist($extra);
                        }

                        ++$totalArticlesProcessed;
                        if (0 == ($totalArticlesProcessed % $batchSize)) {
                            $entityManager->flush();
                            $entityManager->clear();
                        }
                    } catch (\Exception $e) {
                        ++$totalArticlesProcessed;
                        error_log('Error processing article ' . $result['id'] . ': ' . $e->getMessage());
                        continue;
                    }
                }
            }

            // Flush remaining entities
            $entityManager->flush();
            $entityManager->clear();
        } catch (\Exception $e) {
            error_log('postUp error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function unserializeExtraField(?string $data)
    {
        // Handle null or empty data
        if (empty($data)) {
            return null;
        }

        $unserializedData = @unserialize($data);
        if ($unserializedData !== false) {
            return $unserializedData;
        }

        // If unserialize failed, try to fix and retry
        $callback = function ($matches) {
            $matches[2] = trim(preg_replace('/\s\s+/', ' ', $matches[2]));
            return 's:' . mb_strlen($matches[2]) . ':"' . $matches[2] . '";';
        };

        $data = preg_replace_callback('!s:(\d+):"(.*?)";!s', $callback, $data);
        if ($data === null) {
            return null;
        }

        return @unserialize($data);
    }
}
