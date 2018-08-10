<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180809104810 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE swp_slideshow_item (id SERIAL NOT NULL, article_media_id INT NOT NULL, slideshow_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_403ECEB6BF77291E ON swp_slideshow_item (article_media_id)');
        $this->addSql('CREATE INDEX IDX_403ECEB68B14E343 ON swp_slideshow_item (slideshow_id)');
        $this->addSql('ALTER TABLE swp_slideshow_item ADD CONSTRAINT FK_403ECEB6BF77291E FOREIGN KEY (article_media_id) REFERENCES swp_article_media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_slideshow_item ADD CONSTRAINT FK_403ECEB68B14E343 FOREIGN KEY (slideshow_id) REFERENCES swp_slideshow (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE swp_slideshow_item');
    }
}
