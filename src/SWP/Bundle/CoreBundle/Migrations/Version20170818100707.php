<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170818100707 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE swp_article_sources (article_id INT NOT NULL, source_id INT NOT NULL, PRIMARY KEY(article_id, source_id))');
        $this->addSql('CREATE INDEX IDX_E38D33537294869C ON swp_article_sources (article_id)');
        $this->addSql('CREATE INDEX IDX_E38D3353953C1C61 ON swp_article_sources (source_id)');
        $this->addSql('CREATE TABLE swp_article_source (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE swp_article_sources ADD CONSTRAINT FK_E38D33537294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_sources ADD CONSTRAINT FK_E38D3353953C1C61 FOREIGN KEY (source_id) REFERENCES swp_article_source (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article DROP source');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT FK_E38D3353953C1C61');
        $this->addSql('DROP TABLE swp_article_sources');
        $this->addSql('DROP TABLE swp_article_source');
        $this->addSql('ALTER TABLE swp_article ADD source VARCHAR(255) DEFAULT NULL');
    }
}
