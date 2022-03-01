<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180614071345 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE swp_author_media (
          id SERIAL NOT NULL, 
          author_id INT DEFAULT NULL, 
          file_id INT DEFAULT NULL, 
          image_id INT DEFAULT NULL, 
          key VARCHAR(255) NOT NULL, 
          created_at DATE NOT NULL, 
          updated_at DATE DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_44AC8FA7F675F31B ON swp_author_media (author_id)');
        $this->addSql('CREATE INDEX IDX_44AC8FA793CB796C ON swp_author_media (file_id)');
        $this->addSql('CREATE INDEX IDX_44AC8FA73DA5256D ON swp_author_media (image_id)');
        $this->addSql('ALTER TABLE 
          swp_author_media 
        ADD 
          CONSTRAINT FK_44AC8FA7F675F31B FOREIGN KEY (author_id) REFERENCES swp_author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_author_media 
        ADD 
          CONSTRAINT FK_44AC8FA793CB796C FOREIGN KEY (file_id) REFERENCES swp_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_author_media 
        ADD 
          CONSTRAINT FK_44AC8FA73DA5256D FOREIGN KEY (image_id) REFERENCES swp_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_author ADD author_media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE 
          swp_author 
        ADD 
          CONSTRAINT FK_1F96895450FF7C0 FOREIGN KEY (author_media_id) REFERENCES swp_author_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1F96895450FF7C0 ON swp_author (author_media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_author DROP CONSTRAINT FK_1F96895450FF7C0');
        $this->addSql('DROP TABLE swp_author_media');
        $this->addSql('DROP INDEX UNIQ_1F96895450FF7C0');
        $this->addSql('ALTER TABLE swp_author DROP author_media_id');
    }
}
