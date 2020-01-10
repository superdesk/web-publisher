<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200107115725 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_analytics_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_analytics_report (id INT NOT NULL, user_id INT DEFAULT NULL, asset_id VARCHAR(255) NOT NULL, file_extension VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, tenant_code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98E761A9A76ED395 ON swp_analytics_report (user_id)');
        $this->addSql('CREATE UNIQUE INDEX analytics_report_asset_id ON swp_analytics_report (asset_id, tenant_code)');
        $this->addSql('ALTER TABLE swp_analytics_report ADD CONSTRAINT FK_98E761A9A76ED395 FOREIGN KEY (user_id) REFERENCES swp_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_analytics_report_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_analytics_report');
    }
}
