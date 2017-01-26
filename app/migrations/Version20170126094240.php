<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170126094240 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'now\'');
        $this->addSql('ALTER TABLE swp_route ALTER "position" SET NOT NULL');
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
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'2017-01-24 11:36:02.401771\'');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'2017-01-24 11:36:02.39613\'');
        $this->addSql('ALTER TABLE swp_route ALTER position DROP NOT NULL');
    }
}
