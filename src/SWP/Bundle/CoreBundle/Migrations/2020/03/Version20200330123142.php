<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330123142 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_apple_news_config_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_article_apple_news_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_apple_news_config (id INT NOT NULL, tenant_id INT DEFAULT NULL, channel_id VARCHAR(255) NOT NULL, api_key_id VARCHAR(255) NOT NULL, api_key_secret VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C308F3089033212A ON swp_apple_news_config (tenant_id)');
        $this->addSql('CREATE TABLE swp_article_apple_news (id INT NOT NULL, apple_news_article_id VARCHAR(255) NOT NULL, revision_id VARCHAR(255) NOT NULL, share_url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE swp_apple_news_config ADD CONSTRAINT FK_C308F3089033212A FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article ADD apple_news_article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article ALTER is_published_to_apple_news SET NOT NULL');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E858790923B6 FOREIGN KEY (apple_news_article_id) REFERENCES swp_article_apple_news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB21E858790923B6 ON swp_article (apple_news_article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E858790923B6');
        $this->addSql('DROP SEQUENCE swp_apple_news_config_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_apple_news_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_apple_news_config');
        $this->addSql('DROP TABLE swp_article_apple_news');
        $this->addSql('DROP INDEX UNIQ_FB21E858790923B6');
        $this->addSql('ALTER TABLE swp_article DROP apple_news_article_id');
        $this->addSql('ALTER TABLE swp_article ALTER is_published_to_apple_news DROP NOT NULL');
    }
}
