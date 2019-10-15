<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190910125828 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_fb21e85818f9c0d5');
        $this->addSql('CREATE INDEX IDX_FB21E85818F9C0D5 ON swp_article (seo_metadata_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX IDX_FB21E85818F9C0D5');
        $this->addSql('CREATE UNIQUE INDEX uniq_fb21e85818f9c0d5 ON swp_article (seo_metadata_id)');
    }
}
