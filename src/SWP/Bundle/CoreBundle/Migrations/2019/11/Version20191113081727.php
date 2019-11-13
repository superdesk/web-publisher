<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191113081727 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_article_events_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_events');
        $this->addSql('ALTER TABLE swp_author ALTER biography TYPE TEXT');
        $this->addSql('ALTER TABLE swp_author ALTER biography DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_author ALTER biography TYPE TEXT');
        $this->addSql('ALTER TABLE swp_package ALTER genre TYPE TEXT');
        $this->addSql('ALTER TABLE swp_package ALTER genre DROP DEFAULT');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx ON swp_article (
          slug, tenant_code, organization_id
        ) 
        WHERE 
          deleted_at IS NULL');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx ON swp_article (
          slug, tenant_code, organization_id, 
          deleted_at
        ) 
        WHERE 
          deleted_at IS NOT NULL');
        $this->addSql('ALTER TABLE swp_image ALTER length TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE swp_image ALTER length DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_article_events_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_article_events (
          id INT NOT NULL, 
          article_statistics_id INT NOT NULL, 
          route_id INT DEFAULT NULL, 
          article_id INT DEFAULT NULL, 
          action VARCHAR(255) DEFAULT NULL, 
          impression_type VARCHAR(255) DEFAULT NULL, 
          tenant_code VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          page_view_source VARCHAR(255) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_ed5f19e434ecb4e6 ON swp_article_events (route_id)');
        $this->addSql('CREATE INDEX idx_ed5f19e47294869c ON swp_article_events (article_id)');
        $this->addSql('CREATE INDEX idx_ed5f19e41dfe7a17 ON swp_article_events (article_statistics_id)');
        $this->addSql('CREATE INDEX idx_article_events ON swp_article_events (
          article_statistics_id, tenant_code, 
          created_at
        )');
        $this->addSql('ALTER TABLE 
          swp_article_events 
        ADD 
          CONSTRAINT fk_ed5f19e41dfe7a17 FOREIGN KEY (article_statistics_id) REFERENCES swp_article_statistics (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_article_events 
        ADD 
          CONSTRAINT fk_ed5f19e434ecb4e6 FOREIGN KEY (route_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_article_events 
        ADD 
          CONSTRAINT fk_ed5f19e47294869c FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_package ALTER genre TYPE TEXT');
        $this->addSql('ALTER TABLE swp_package ALTER genre DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_image ALTER length TYPE NUMERIC(10, 0)');
        $this->addSql('ALTER TABLE swp_image ALTER length DROP DEFAULT');
        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx ON swp_article (
          slug, tenant_code, organization_id, 
          deleted_at
        ) 
        WHERE 
          (deleted_at IS NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx ON swp_article (
          slug, tenant_code, organization_id
        ) 
        WHERE 
          (deleted_at IS NULL)');
        $this->addSql('ALTER TABLE swp_author ALTER biography TYPE VARCHAR(460)');
        $this->addSql('ALTER TABLE swp_author ALTER biography DROP DEFAULT');
    }
}
