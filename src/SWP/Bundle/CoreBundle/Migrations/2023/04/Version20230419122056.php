<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419122056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unused fields from swp_user tale. This comes as part of fixing old migrations.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );
        $this->addSql('DROP INDEX IF EXISTS uniq_7384fb3192fc23a8');
        $this->addSql('DROP INDEX IF EXISTS uniq_7384fb31a0d96fbf');
        $this->addSql('ALTER TABLE swp_user DROP IF EXISTS username_canonical');
        $this->addSql('ALTER TABLE swp_user DROP IF EXISTS email_canonical');
        $this->addSql('ALTER TABLE swp_user DROP IF EXISTS salt');
        $this->addSql('ALTER TABLE swp_user DROP IF EXISTS last_login');
        $this->addSql('ALTER TABLE swp_user DROP IF EXISTS password_requested_at');
        $this->addSql('ALTER TABLE swp_user RENAME COLUMN enabled TO is_verified');

    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );
        $this->addSql('ALTER TABLE swp_user ADD IF NOT EXISTS username_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD IF NOT EXISTS email_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD IF NOT EXISTS salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD IF NOT EXISTS last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD IF NOT EXISTS password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user RENAME COLUMN is_verified TO enabled');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_7384fb3192fc23a8 ON swp_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_7384fb31a0d96fbf ON swp_user (email_canonical)');
    }
}
