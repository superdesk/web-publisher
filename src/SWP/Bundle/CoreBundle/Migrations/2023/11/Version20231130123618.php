<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130123618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE swp_route ALTER COLUMN description TYPE TEXT USING description::TEXT; ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE swp_route ALTER COLUMN description TYPE VARCHAR(255)');
    }
}
