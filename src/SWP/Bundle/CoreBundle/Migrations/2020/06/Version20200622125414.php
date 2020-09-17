<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200622125414 extends AbstractMigration implements ContainerAwareInterface
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
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_article_metadata_place_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_article_metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_article_metadata_service_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_article_metadata_subject_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_article_metadata_place (id INT NOT NULL, metadata_id INT DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, world_region VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, qgroup VARCHAR(255) DEFAULT NULL, qcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C173802DC9EE959 ON swp_article_metadata_place (metadata_id)');
        $this->addSql('CREATE TABLE swp_article_metadata (id INT NOT NULL, article_id INT DEFAULT NULL, profile VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, urgency INT DEFAULT NULL, ed_note VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, guid VARCHAR(255) DEFAULT NULL, located VARCHAR(255) DEFAULT NULL, byline VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EEF4773C7294869C ON swp_article_metadata (article_id)');
        $this->addSql('CREATE TABLE swp_article_metadata_service (id INT NOT NULL, metadata_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_779FF189DC9EE959 ON swp_article_metadata_service (metadata_id)');
        $this->addSql('CREATE TABLE swp_article_metadata_subject (id INT NOT NULL, metadata_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, scheme VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6DCC5521DC9EE959 ON swp_article_metadata_subject (metadata_id)');
        $this->addSql('ALTER TABLE swp_article_metadata_place ADD CONSTRAINT FK_6C173802DC9EE959 FOREIGN KEY (metadata_id) REFERENCES swp_article_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_metadata ADD CONSTRAINT FK_EEF4773C7294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_metadata_service ADD CONSTRAINT FK_779FF189DC9EE959 FOREIGN KEY (metadata_id) REFERENCES swp_article_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_metadata_subject ADD CONSTRAINT FK_6DCC5521DC9EE959 FOREIGN KEY (metadata_id) REFERENCES swp_article_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_item ADD profile VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_route ADD description VARCHAR(255) DEFAULT NULL');
    }

    public function postUp(Schema $schema): void
    {
        $metadataFactory = $this->container->get('swp.factory.metadata');
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        $batchSize = 500;
        $numberOfRecordsPerPage = 2000;

        $totalArticles = $entityManager
            ->createQuery('SELECT count(a) FROM SWP\Bundle\CoreBundle\Model\Article a')
            ->getSingleScalarResult();

        $totalArticlesProcessed = 0;
        $isProcessing = true;

        while ($isProcessing) {
            $query = $entityManager->createQuery('SELECT a FROM SWP\Bundle\CoreBundle\Model\Article a')
                ->setMaxResults($numberOfRecordsPerPage)
                ->setFirstResult($totalArticlesProcessed);

            echo 'fetching $numberOfRecordsPerPage starting from $totalArticlesProcessed\n';

            $iterableResult = $query->iterate();

            while (false !== ($row = $iterableResult->next())) {
                $article = $row[0];
                $legacyMetadata = $article->getMetadata();
                if (empty($legacyMetadata)) {
                    continue;
                }

                $metadata = $metadataFactory->createFrom($legacyMetadata);

                $entityManager->persist($metadata);

                $article->setData($metadata);

                echo 'new metadata persisted\n';

                if (0 === ($totalArticlesProcessed % $batchSize)) {
                    $entityManager->flush();
                    $entityManager->clear();
                    echo 'batch flushed\n';
                }

                ++$totalArticlesProcessed;
            }

            if ($totalArticlesProcessed === $totalArticles) {
                break;
            }
        }

        $entityManager->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_metadata_place DROP CONSTRAINT FK_6C173802DC9EE959');
        $this->addSql('ALTER TABLE swp_article_metadata_service DROP CONSTRAINT FK_779FF189DC9EE959');
        $this->addSql('ALTER TABLE swp_article_metadata_subject DROP CONSTRAINT FK_6DCC5521DC9EE959');
        $this->addSql('DROP SEQUENCE swp_article_metadata_place_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_metadata_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_metadata_service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_metadata_subject_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_metadata_place');
        $this->addSql('DROP TABLE swp_article_metadata');
        $this->addSql('DROP TABLE swp_article_metadata_service');
        $this->addSql('DROP TABLE swp_article_metadata_subject');
        $this->addSql('ALTER TABLE swp_item DROP profile');
        $this->addSql('ALTER TABLE swp_route DROP description');
    }
}
