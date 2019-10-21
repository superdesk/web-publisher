<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191021123527 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DELETE FROM swp_article_media USING swp_slideshow_item WHERE swp_article_media.id = swp_slideshow_item.article_media_id AND swp_slideshow_item.deleted_at IS NOT NULL');
        $this->addSql('DELETE FROM swp_slideshow_item WHERE deleted_at IS NOT NULL');
        $this->addSql('DELETE FROM swp_slideshow WHERE deleted_at IS NOT NULL');
        $this->addSql('ALTER TABLE swp_slideshow_item DROP deleted_at');
        $this->addSql('ALTER TABLE swp_slideshow DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_slideshow_item ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_slideshow ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
