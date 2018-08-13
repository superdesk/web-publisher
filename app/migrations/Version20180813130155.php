<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180813130155 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_item_groups_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_item_groups (id INT NOT NULL, package_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A658EE1DF44CABFF ON swp_item_groups (package_id)');
        $this->addSql('CREATE TABLE swp_slideshow (id SERIAL NOT NULL, article_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6F1C413B7294869C ON swp_slideshow (article_id)');
        $this->addSql('ALTER TABLE swp_item_groups ADD CONSTRAINT FK_A658EE1DF44CABFF FOREIGN KEY (package_id) REFERENCES swp_package (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_slideshow ADD CONSTRAINT FK_6F1C413B7294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_item ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_item ADD CONSTRAINT FK_E10C0866FE54D947 FOREIGN KEY (group_id) REFERENCES swp_item_groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E10C0866FE54D947 ON swp_item (group_id)');

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

        $this->addSql('ALTER TABLE swp_item DROP CONSTRAINT FK_E10C0866FE54D947');
        $this->addSql('DROP SEQUENCE swp_item_groups_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_item_groups');
        $this->addSql('DROP TABLE swp_slideshow');
        $this->addSql('DROP INDEX IDX_E10C0866FE54D947');
        $this->addSql('ALTER TABLE swp_item DROP group_id');

        $this->addSql('DROP TABLE swp_slideshow_item');
    }
}
