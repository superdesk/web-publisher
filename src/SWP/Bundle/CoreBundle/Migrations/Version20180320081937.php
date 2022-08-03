<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180320081937 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_package_preview_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_package_preview_token (id INT NOT NULL, route_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, body TEXT NOT NULL, tenant_code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AD1CA87234ECB4E6 ON swp_package_preview_token (route_id)');
        $this->addSql('ALTER TABLE swp_package_preview_token ADD CONSTRAINT FK_AD1CA87234ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_package_preview_token_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_package_preview_token');
    }
}
