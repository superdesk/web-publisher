<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200326140105 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_apple_news_config_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_apple_news_config (id INT NOT NULL, tenant_id INT DEFAULT NULL, channel_id VARCHAR(255) NOT NULL, api_key_id VARCHAR(255) NOT NULL, api_key_secret VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C308F3089033212A ON swp_apple_news_config (tenant_id)');
        $this->addSql('ALTER TABLE swp_apple_news_config ADD CONSTRAINT FK_C308F3089033212A FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_apple_news_config_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_apple_news_config');
    }
}
