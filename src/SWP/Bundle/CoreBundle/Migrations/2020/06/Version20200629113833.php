<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200629113833 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_apple_news_config ALTER channel_id DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_apple_news_config ALTER api_key_id DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_apple_news_config ALTER api_key_secret DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_apple_news_config ALTER channel_id SET NOT NULL');
        $this->addSql('ALTER TABLE swp_apple_news_config ALTER api_key_id SET NOT NULL');
        $this->addSql('ALTER TABLE swp_apple_news_config ALTER api_key_secret SET NOT NULL');
    }
}
