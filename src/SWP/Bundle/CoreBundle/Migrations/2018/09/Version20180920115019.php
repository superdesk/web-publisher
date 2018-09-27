<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180920115019 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_keyword_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_keyword (
          id INT NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          slug VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE swp_article_keyword (
          article_id INT NOT NULL, 
          keyword_id INT NOT NULL, 
          PRIMARY KEY(article_id, keyword_id)
        )');
        $this->addSql('CREATE INDEX IDX_6B43279A7294869C ON swp_article_keyword (article_id)');
        $this->addSql('CREATE INDEX IDX_6B43279A115D4552 ON swp_article_keyword (keyword_id)');
        $this->addSql('ALTER TABLE 
          swp_article_keyword 
        ADD 
          CONSTRAINT FK_6B43279A7294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_article_keyword 
        ADD 
          CONSTRAINT FK_6B43279A115D4552 FOREIGN KEY (keyword_id) REFERENCES swp_keyword (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_keyword DROP CONSTRAINT FK_6B43279A115D4552');
        $this->addSql('DROP SEQUENCE swp_keyword_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_keyword');
        $this->addSql('DROP TABLE swp_article_keyword');
    }
}
