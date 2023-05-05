<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207132939 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('ALTER TABLE swp_article ADD COLUMN IF NOT EXITS feature_media INT DEFAULT NULL');

        //$this->addSql('ALTER TABLE swp_article ADD feature_media INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E858A372AB05 FOREIGN KEY (feature_media) REFERENCES swp_article_media (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FB21E858A372AB05 ON swp_article (feature_media)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E858A372AB05');
        $this->addSql('DROP INDEX IDX_FB21E858A372AB05');
        $this->addSql('ALTER TABLE swp_article DROP feature_media');
    }
}
