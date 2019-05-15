<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190429114044 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_article_seo_media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_article_seo_metadata_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_article_seo_media (id INT NOT NULL, image_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AE10DDE73DA5256D ON swp_article_seo_media (image_id)');
        $this->addSql('CREATE TABLE swp_article_seo_metadata (id INT NOT NULL, seo_meta_media_id INT DEFAULT NULL, seo_og_media_id INT DEFAULT NULL, seo_twitter_media_id INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, og_title VARCHAR(255) DEFAULT NULL, og_description VARCHAR(255) DEFAULT NULL, twitter_title VARCHAR(255) DEFAULT NULL, twitter_description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_464641EC7156AE72 ON swp_article_seo_metadata (seo_meta_media_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_464641EC7DA1692C ON swp_article_seo_metadata (seo_og_media_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_464641ECBF702C21 ON swp_article_seo_metadata (seo_twitter_media_id)');
        $this->addSql('ALTER TABLE swp_article_seo_media ADD CONSTRAINT FK_AE10DDE73DA5256D FOREIGN KEY (image_id) REFERENCES swp_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_seo_metadata ADD CONSTRAINT FK_464641EC7156AE72 FOREIGN KEY (seo_meta_media_id) REFERENCES swp_article_seo_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_seo_metadata ADD CONSTRAINT FK_464641EC7DA1692C FOREIGN KEY (seo_og_media_id) REFERENCES swp_article_seo_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_seo_metadata ADD CONSTRAINT FK_464641ECBF702C21 FOREIGN KEY (seo_twitter_media_id) REFERENCES swp_article_seo_media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article ADD seo_metadata_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E85818F9C0D5 FOREIGN KEY (seo_metadata_id) REFERENCES swp_article_seo_metadata (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB21E85818F9C0D5 ON swp_article (seo_metadata_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_seo_metadata DROP CONSTRAINT FK_464641EC7156AE72');
        $this->addSql('ALTER TABLE swp_article_seo_metadata DROP CONSTRAINT FK_464641EC7DA1692C');
        $this->addSql('ALTER TABLE swp_article_seo_metadata DROP CONSTRAINT FK_464641ECBF702C21');
        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E85818F9C0D5');
        $this->addSql('DROP SEQUENCE swp_article_seo_media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_article_seo_metadata_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_seo_media');
        $this->addSql('DROP TABLE swp_article_seo_metadata');
        $this->addSql('DROP INDEX UNIQ_FB21E85818F9C0D5');
        $this->addSql('ALTER TABLE swp_article DROP seo_metadata_id');
    }
}
