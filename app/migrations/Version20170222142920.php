<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170222142920 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item ADD evolved_from VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_package ADD evolved_from VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article ADD code VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE swp_article SET code = md5(random()::text)');
        $this->addSql('ALTER TABLE swp_article ALTER COLUMN code SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item DROP evolved_from');
        $this->addSql('ALTER TABLE swp_package DROP evolved_from');
        $this->addSql('ALTER TABLE swp_article DROP code');
    }
}
