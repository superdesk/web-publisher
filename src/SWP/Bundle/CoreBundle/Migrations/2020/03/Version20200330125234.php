<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330125234 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E858790923B6');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E858790923B6 FOREIGN KEY (apple_news_article_id) REFERENCES swp_article_apple_news (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_failed_queue_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT fk_fb21e858790923b6');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT fk_fb21e858790923b6 FOREIGN KEY (apple_news_article_id) REFERENCES swp_article_apple_news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
