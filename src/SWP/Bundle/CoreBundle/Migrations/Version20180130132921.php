<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180130132921 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item ADD extra TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN swp_item.extra IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_package ADD extra TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN swp_package.extra IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_article ADD extra TEXT DEFAULT \'a:0:{}\'');
        $this->addSql('COMMENT ON COLUMN swp_article.extra IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_item DROP extra');
        $this->addSql('ALTER TABLE swp_package DROP extra');
        $this->addSql('ALTER TABLE swp_article DROP extra');
    }
}
