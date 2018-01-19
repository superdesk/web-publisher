<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180118145935 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_package_author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_package_author (id INT NOT NULL, package_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, biography VARCHAR(255) DEFAULT NULL, job_title TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_574B723AF44CABFF ON swp_package_author (package_id)');
        $this->addSql('COMMENT ON COLUMN swp_package_author.job_title IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT FK_574B723AF44CABFF FOREIGN KEY (package_id) REFERENCES swp_package (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_package_author_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_package_author');
    }
}
