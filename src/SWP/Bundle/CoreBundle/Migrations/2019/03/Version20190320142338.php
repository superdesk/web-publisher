<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320142338 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item ADD body_text TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_item ADD usage_terms VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_item ADD copyright_notice VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_item ADD copyright_holder VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article_media ADD headline TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article_media ADD copyright_notice VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article_media ADD copyright_holder VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item DROP body_text');
        $this->addSql('ALTER TABLE swp_item DROP usage_terms');
        $this->addSql('ALTER TABLE swp_item DROP copyright_notice');
        $this->addSql('ALTER TABLE swp_item DROP copyright_holder');
        $this->addSql('ALTER TABLE swp_article_media DROP headline');
        $this->addSql('ALTER TABLE swp_article_media DROP copyright_notice');
        $this->addSql('ALTER TABLE swp_article_media DROP copyright_holder');
    }
}
