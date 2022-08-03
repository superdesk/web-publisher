<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171215124825 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_article_events_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_article_statistics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_article_events (id INT NOT NULL, article_statistics_id INT NOT NULL, action VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ED5F19E41DFE7A17 ON swp_article_events (article_statistics_id)');
        $this->addSql('CREATE TABLE swp_article_statistics (id INT NOT NULL, article_id INT NOT NULL, impressions_number INT DEFAULT NULL, page_views_number INT DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_505B94427294869C ON swp_article_statistics (article_id)');
        $this->addSql('ALTER TABLE swp_article_events ADD CONSTRAINT FK_ED5F19E41DFE7A17 FOREIGN KEY (article_statistics_id) REFERENCES swp_article_statistics (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_statistics ADD CONSTRAINT FK_505B94427294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_events DROP CONSTRAINT FK_ED5F19E41DFE7A17');
        $this->addSql('DROP SEQUENCE swp_article_events_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_statistics_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_events');
        $this->addSql('DROP TABLE swp_article_statistics');
    }
}
