<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419133319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_verified column in swp_user table. This column may already exist on same instances.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('ALTER TABLE IF EXISTS swp_user ADD COLUMN IF NOT EXISTS is_verified BOOLEAN DEFAULT \'false\' NOT NULL;
');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('ALTER TABLE swp_user DROP COLUMN IF EXISTS is_verified');
    }
}
