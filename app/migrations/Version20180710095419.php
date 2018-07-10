<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180710095419 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_events ADD route_id INT');
        $this->addSql('ALTER TABLE swp_article_events ADD article_id INT');
        $this->addSql('ALTER TABLE swp_article_events RENAME COLUMN value TO impression_type');
        $this->addSql('ALTER TABLE 
          swp_article_events 
        ADD 
          CONSTRAINT FK_ED5F19E434ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_article_events 
        ADD 
          CONSTRAINT FK_ED5F19E47294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_ED5F19E434ECB4E6 ON swp_article_events (route_id)');
        $this->addSql('CREATE INDEX IDX_ED5F19E47294869C ON swp_article_events (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_events DROP CONSTRAINT FK_ED5F19E434ECB4E6');
        $this->addSql('ALTER TABLE swp_article_events DROP CONSTRAINT FK_ED5F19E47294869C');
        $this->addSql('DROP INDEX IDX_ED5F19E434ECB4E6');
        $this->addSql('DROP INDEX IDX_ED5F19E47294869C');
        $this->addSql('ALTER TABLE swp_article_events DROP route_id');
        $this->addSql('ALTER TABLE swp_article_events DROP article_id');
        $this->addSql('ALTER TABLE swp_article_events RENAME COLUMN impression_type TO value');
    }
}
