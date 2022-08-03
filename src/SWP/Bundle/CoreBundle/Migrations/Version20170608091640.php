<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170608091640 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_rule ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_rule ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE swp_article SET package_id = (SELECT p.id FROM swp_package AS p WHERE p.guid = code ORDER BY p.id DESC LIMIT 1)');
        $this->addSql('delete from swp_article where is_publishable = false and package_id = (select p.id from swp_package as p where p.guid = code order by p.id desc limit 1)');
        $this->addSql('update swp_package set status = \'published\' where (select count(*) from swp_article as a where a.package_id = id) > 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_rule DROP description');
        $this->addSql('ALTER TABLE swp_rule DROP name');
    }
}
