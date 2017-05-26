<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170512132743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_user ADD first_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD about TEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_user DROP first_name');
        $this->addSql('ALTER TABLE swp_user DROP last_name');
        $this->addSql('ALTER TABLE swp_user DROP about');
    }
}
