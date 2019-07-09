<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190709090034 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX swp_article_slug_idx');
        $this->addSql('
            CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx 
            ON swp_article (
              slug, tenant_code, organization_id, 
              deleted_at
            ) 
            WHERE deleted_at IS NOT NULL
        ');

        $this->addSql('
            CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx 
            ON swp_article (
              slug, tenant_code, organization_id
            ) 
            WHERE deleted_at IS NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_idx ON swp_article (
          slug, tenant_code, organization_id, 
          deleted_at
        )');
    }
}
