<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251113190202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change by_line/byline columns from VARCHAR(255) to TEXT to support longer byline values.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_media ALTER by_line TYPE TEXT');
        $this->addSql('ALTER TABLE swp_article_metadata ALTER byline TYPE TEXT');
        $this->addSql('ALTER TABLE swp_item ALTER byline TYPE TEXT');
        $this->addSql('ALTER TABLE swp_package ALTER byline TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_media ALTER by_line TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE swp_article_metadata ALTER byline TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE swp_item ALTER byline TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE swp_package ALTER byline TYPE VARCHAR(255)');
    }
}

