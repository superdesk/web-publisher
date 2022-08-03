<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180122115426 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_author (id INT NOT NULL, name VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, biography VARCHAR(255) DEFAULT NULL, job_title TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN swp_author.job_title IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE swp_package_author (package_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(package_id, author_id))');
        $this->addSql('CREATE INDEX IDX_574B723AF44CABFF ON swp_package_author (package_id)');
        $this->addSql('CREATE INDEX IDX_574B723AF675F31B ON swp_package_author (author_id)');
        $this->addSql('CREATE TABLE swp_article_author (article_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(article_id, author_id))');
        $this->addSql('CREATE INDEX IDX_37796667294869C ON swp_article_author (article_id)');
        $this->addSql('CREATE INDEX IDX_3779666F675F31B ON swp_article_author (author_id)');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT FK_574B723AF44CABFF FOREIGN KEY (package_id) REFERENCES swp_package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT FK_574B723AF675F31B FOREIGN KEY (author_id) REFERENCES swp_author (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_author ADD CONSTRAINT FK_37796667294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_author ADD CONSTRAINT FK_3779666F675F31B FOREIGN KEY (author_id) REFERENCES swp_author (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_author DROP CONSTRAINT FK_574B723AF675F31B');
        $this->addSql('ALTER TABLE swp_article_author DROP CONSTRAINT FK_3779666F675F31B');
        $this->addSql('DROP SEQUENCE swp_author_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_author');
        $this->addSql('DROP TABLE swp_package_author');
        $this->addSql('DROP TABLE swp_article_author');
    }
}
