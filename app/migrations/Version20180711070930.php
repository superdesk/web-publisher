<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180711070930 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_subscription (id INT NOT NULL, user_id INT NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, details TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BCE6AB577153098 ON swp_subscription (code)');
        $this->addSql('CREATE INDEX IDX_9BCE6AB5A76ED395 ON swp_subscription (user_id)');
        $this->addSql('COMMENT ON COLUMN swp_subscription.details IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_subscription ADD CONSTRAINT FK_9BCE6AB5A76ED395 FOREIGN KEY (user_id) REFERENCES swp_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_subscription_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_subscription');
    }
}
