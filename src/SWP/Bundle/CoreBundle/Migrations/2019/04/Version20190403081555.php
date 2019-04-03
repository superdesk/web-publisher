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

        $this->addSql('ALTER TABLE swp_article_media DROP CONSTRAINT FK_B9721F7E7294869C');
        $this->addSql('ALTER TABLE swp_article_media DROP deleted_at');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE 
          swp_article_media 
        ADD 
          CONSTRAINT FK_B9721F7E7294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE 
        SET 
          NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_media DROP CONSTRAINT fk_b9721f7e7294869c');
        $this->addSql('ALTER TABLE swp_article_media ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at TYPE DATE');
        $this->addSql('ALTER TABLE swp_article_media ALTER updated_at DROP DEFAULT');
        $this->addSql('ALTER TABLE 
          swp_article_media 
        ADD 
          CONSTRAINT fk_b9721f7e7294869c FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
