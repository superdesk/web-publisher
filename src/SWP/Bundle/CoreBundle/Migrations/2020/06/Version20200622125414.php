<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use SWP\Bundle\ContentBundle\Model\Metadata;
use SWP\Bundle\ContentBundle\Model\MetadataInterface;
use SWP\Bundle\ContentBundle\Model\Place;
use SWP\Bundle\ContentBundle\Model\Service;
use SWP\Bundle\ContentBundle\Model\Subject;
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
        $this->addSql('CREATE TABLE swp_article_metadata (id INT NOT NULL, article_id INT DEFAULT NULL, profile VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, urgency INT DEFAULT NULL, ed_note VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, guid VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EEF4773C7294869C ON swp_article_metadata (article_id)');
        $this->addSql('CREATE TABLE swp_article_metadata_service (id INT NOT NULL, metadata_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_779FF189DC9EE959 ON swp_article_metadata_service (metadata_id)');
        $this->addSql('CREATE TABLE swp_article_metadata_subject (id INT NOT NULL, metadata_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, scheme VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6DCC5521DC9EE959 ON swp_article_metadata_subject (metadata_id)');
        $this->addSql('ALTER TABLE swp_article_metadata_place ADD CONSTRAINT FK_6C173802DC9EE959 FOREIGN KEY (metadata_id) REFERENCES swp_article_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_metadata ADD CONSTRAINT FK_EEF4773C7294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_metadata_service ADD CONSTRAINT FK_779FF189DC9EE959 FOREIGN KEY (metadata_id) REFERENCES swp_article_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_metadata_subject ADD CONSTRAINT FK_6DCC5521DC9EE959 FOREIGN KEY (metadata_id) REFERENCES swp_article_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_item ADD profile VARCHAR(255) DEFAULT NULL');
    }

    public function postUp(Schema $schema): void
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT a FROM SWP\Bundle\ContentBundle\Model\Article a');

        $batchSize = 20;
        $i = 1;
        $iterableResult = $query->iterate();
        foreach ($iterableResult as $row) {
            $article = $row[0];
            $legacyMetadata = $article->getMetadata();
            if (empty($legacyMetadata)) {
                continue;
            }

            $metadata = $this->convertLegacyMetadata($legacyMetadata);

            $entityManager->persist($metadata);

            $article->setData($metadata);

            if (0 === ($i % $batchSize)) {
                $entityManager->flush();
                $entityManager->clear();
            }
            ++$i;
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
        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E858DC9EE959');
        $this->addSql('DROP SEQUENCE swp_article_metadata_place_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_metadata_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_metadata_service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_metadata_subject_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_metadata_place');
        $this->addSql('DROP TABLE swp_article_metadata');
        $this->addSql('DROP TABLE swp_article_metadata_service');
        $this->addSql('DROP TABLE swp_article_metadata_subject');
        $this->addSql('ALTER TABLE swp_item DROP profile');
    }

    private function convertLegacyMetadata(array $legacyMetadata): MetadataInterface
    {
        $metadata = new Metadata();
        if (isset($legacyMetadata['subject'])) {
            foreach ($legacyMetadata['subject'] as $legacySubject) {
                $subject = new Subject();
                $subject->setCode($legacySubject['code']);
                $subject->setScheme($legacySubject['scheme'] ?? null);

                $metadata->addSubject($subject);
            }
        }

        if (isset($legacyMetadata['service'])) {
            foreach ($legacyMetadata['service'] as $legacyService) {
                $service = new Service();
                $service->setCode($legacyService['code']);

                $metadata->addService($service);
            }
        }

        if (isset($legacyMetadata['place'])) {
            foreach ($legacyMetadata['place'] as $legacyPlace) {
                $place = new Place();
                $place->setCountry($legacyPlace['country'] ?? null);
                $place->setGroup($legacyPlace['group'] ?? null);
                $place->setName($legacyPlace['name'] ?? null);
                $place->setState($legacyPlace['state'] ?? null);
                $place->setQcode($legacyPlace['qcode'] ?? null);
                $place->setWorldRegion($legacyPlace['world_region'] ?? null);

                $metadata->addPlace($place);
            }
        }

        $metadata->setProfile($legacyMetadata['profile'] ?? null);
        $metadata->setUrgency($legacyMetadata['urgency'] ?? null);
        $metadata->setPriority($legacyMetadata['priority'] ?? null);
        $metadata->setEdNote($legacyMetadata['edNote'] ?? null);
        $metadata->setLanguage($legacyMetadata['language'] ?? null);
        $metadata->setGuid($legacyMetadata['guid'] ?? null);

        return $metadata;
    }
}
