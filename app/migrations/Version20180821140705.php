<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180821140705 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article ALTER COLUMN paywall_secured SET DEFAULT FALSE');
        $this->addSql('ALTER TABLE swp_route ALTER COLUMN paywall_secured SET DEFAULT FALSE');
        $this->addSql('UPDATE swp_article SET paywall_secured = false WHERE paywall_secured IS NULL');
        $this->addSql('UPDATE swp_route SET paywall_secured = false WHERE paywall_secured IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article ALTER paywall_secured DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_route ALTER paywall_secured DROP DEFAULT');
    }
}
