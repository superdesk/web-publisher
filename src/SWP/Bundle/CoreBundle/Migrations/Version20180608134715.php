<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180608134715 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_route DROP CONSTRAINT fk_5ce4ce5a79066886');
        $this->addSql('DROP INDEX idx_5ce4ce5a79066886');
        $this->addSql('ALTER TABLE swp_route DROP root_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_route ADD root_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_route ADD CONSTRAINT fk_5ce4ce5a79066886 FOREIGN KEY (root_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5ce4ce5a79066886 ON swp_route (root_id)');
    }
}
