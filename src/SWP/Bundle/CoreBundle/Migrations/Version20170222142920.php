<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170222142920 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item ADD evolved_from VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_package ADD evolved_from VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article ADD code VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE swp_article SET code = md5(random()::text)');
        $this->addSql('UPDATE swp_article a SET code = p.guid FROM swp_package p WHERE a.slug = LOWER(replace(p.slugline, \' \', \'-\'))');
        $this->addSql('ALTER TABLE swp_article ALTER COLUMN code SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item DROP evolved_from');
        $this->addSql('ALTER TABLE swp_package DROP evolved_from');
        $this->addSql('ALTER TABLE swp_article DROP code');
    }
}
