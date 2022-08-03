<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170829142821 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('ALTER TABLE swp_article_sources ADD id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT swp_article_sources_pkey');
        $this->addSql('ALTER TABLE swp_article_sources ADD PRIMARY KEY (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT swp_article_sources_pkey');
        $this->addSql('ALTER TABLE swp_article_sources DROP id');
        $this->addSql('ALTER TABLE swp_article_sources ADD PRIMARY KEY (article_id, source_id)');
    }
}
