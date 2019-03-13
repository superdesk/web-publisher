<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190208103320 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE request_metrics_id_seq CASCADE');
        $this->addSql('DROP TABLE request_metrics');
        $this->addSql('ALTER TABLE swp_publish_destination ADD content_lists TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN swp_publish_destination.content_lists IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE request_metrics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE request_metrics (
          id INT NOT NULL, 
          uri TEXT NOT NULL, 
          route VARCHAR(255) NOT NULL, 
          duration INT NOT NULL, 
          created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('ALTER TABLE swp_publish_destination DROP content_lists');
    }
}
