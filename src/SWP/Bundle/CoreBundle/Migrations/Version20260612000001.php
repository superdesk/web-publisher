<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Removes Facebook Instant Articles support.
 */
final class Version20260612000001 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS swp_fbia_article');
        $this->addSql('DROP TABLE IF EXISTS swp_fbia_feed');
        $this->addSql('DROP TABLE IF EXISTS swp_fbia_page');
        $this->addSql('DROP TABLE IF EXISTS swp_fbia_application');
        $this->addSql('ALTER TABLE swp_article DROP COLUMN IF EXISTS is_published_fbia');
        $this->addSql('ALTER TABLE swp_publish_destination DROP COLUMN IF EXISTS fbia');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE swp_article ADD is_published_fbia BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_publish_destination ADD fbia BOOLEAN DEFAULT false NOT NULL');
        $this->throwIrreversibleMigrationException('FBIA tables are not recreated.');
    }
}
