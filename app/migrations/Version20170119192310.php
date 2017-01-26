<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170119192310 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_media ALTER tenant_code SET NOT NULL');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'now\'');
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'now\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE swp_article_media ALTER tenant_code DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'2017-01-19 13:48:34.70216\'');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'2017-01-19 13:48:34.694931\'');
    }
}
