<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180808094029 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX swp_status_route ON swp_article (status, route_id)');
        $this->addSql('CREATE INDEX swp_deleted_tenant ON swp_article (deleted_at, tenant_code)');
        $this->addSql('CREATE INDEX swp_status_deleted_route_tenant ON swp_article (
          status, tenant_code, deleted_at, route_id
        )');
        $this->addSql('CREATE INDEX swp_deleted_at ON swp_article (deleted_at)');
        $this->addSql('CREATE INDEX swp_count_route ON swp_article (deleted_at, status, tenant_code)');
        $this->addSql('CREATE INDEX swp_external_deleted_tenant ON swp_article_external (deleted_at, tenant_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX swp_status_route');
        $this->addSql('DROP INDEX swp_deleted_tenant');
        $this->addSql('DROP INDEX swp_status_deleted_route_tenant');
        $this->addSql('DROP INDEX swp_deleted_at');
        $this->addSql('DROP INDEX swp_count_route');
        $this->addSql('DROP INDEX swp_external_deleted_tenant');
    }
}
