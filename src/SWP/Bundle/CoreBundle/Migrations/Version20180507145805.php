<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180507145805 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE swp_article_external (
          id SERIAL NOT NULL, 
          article_id INT DEFAULT NULL, 
          external_id VARCHAR(255) NOT NULL, 
          live_url VARCHAR(255) DEFAULT NULL, 
          status VARCHAR(255) NOT NULL, 
          published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          unpublished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          tenant_code VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F9B2E2907294869C ON swp_article_external (article_id)');
        $this->addSql('ALTER TABLE 
          swp_article_external 
        ADD 
          CONSTRAINT FK_F9B2E2907294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE swp_article_external');
    }
}
