<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250814120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), "Migration can only be executed safely on 'postgresql'.");

        $this->addSql('ALTER TABLE swp_article_media ALTER by_line TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), "Migration can only be executed safely on 'postgresql'.");

        $this->addSql('ALTER TABLE swp_article_media ALTER by_line TYPE VARCHAR(255)');
    }
}

