<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403081555 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('
            UPDATE 
                swp_article 
            SET 
                feature_media = NULL
            WHERE 
                  feature_media IN (
                    SELECT id FROM swp_article_media WHERE deleted_at IS NOT NULL 
                  )
        ');
        $this->addSql('DELETE FROM swp_article_media WHERE deleted_at IS NOT NULL');
        $this->addSql('ALTER TABLE swp_article_media DROP deleted_at');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_media ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at TYPE DATE');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at DROP DEFAULT');
    }
}
