<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170525105348 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_user ADD organization_id INT');
        $this->addSql('ALTER TABLE swp_user ADD CONSTRAINT FK_7384FB3132C8A3DE FOREIGN KEY (organization_id) REFERENCES swp_organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7384FB3132C8A3DE ON swp_user (organization_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_user DROP CONSTRAINT FK_7384FB3132C8A3DE');
        $this->addSql('DROP INDEX IDX_7384FB3132C8A3DE');
        $this->addSql('ALTER TABLE swp_user DROP organization_id');
    }
}
