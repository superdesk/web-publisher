<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Removes Google AMP HTML support (per-tenant amp_enabled flag).
 */
final class Version20260612000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE swp_tenant DROP COLUMN IF EXISTS amp_enabled');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE swp_tenant ADD amp_enabled BOOLEAN DEFAULT false NOT NULL');
    }
}
