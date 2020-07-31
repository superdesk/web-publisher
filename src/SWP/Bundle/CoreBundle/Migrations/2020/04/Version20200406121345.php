<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406121345 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_author DROP CONSTRAINT FK_37796667294869C');
        $this->addSql('ALTER TABLE swp_article_author DROP CONSTRAINT FK_3779666F675F31B');
        $this->addSql('ALTER TABLE swp_article_author ADD CONSTRAINT FK_37796667294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_author ADD CONSTRAINT FK_3779666F675F31B FOREIGN KEY (author_id) REFERENCES swp_author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_author DROP CONSTRAINT fk_37796667294869c');
        $this->addSql('ALTER TABLE swp_article_author DROP CONSTRAINT fk_3779666f675f31b');
        $this->addSql('ALTER TABLE swp_article_author ADD CONSTRAINT fk_37796667294869c FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_author ADD CONSTRAINT fk_3779666f675f31b FOREIGN KEY (author_id) REFERENCES swp_author (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
