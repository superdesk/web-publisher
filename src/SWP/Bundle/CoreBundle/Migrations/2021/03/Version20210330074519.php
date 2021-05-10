<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330074519 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

//        $this->addSql('ALTER TABLE swp_redirect_route DROP CONSTRAINT FK_8681EEEE397EC969');
//        $this->addSql('DROP INDEX idx_8681eeee397ec969');
//        $this->addSql('ALTER TABLE swp_redirect_route ADD CONSTRAINT FK_8681EEEE397EC969 FOREIGN KEY (route_source_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_8681EEEE397EC969 ON swp_redirect_route (route_source_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_redirect_route DROP CONSTRAINT fk_8681eeee397ec969');
        $this->addSql('DROP INDEX UNIQ_8681EEEE397EC969');
        $this->addSql('ALTER TABLE swp_redirect_route ADD CONSTRAINT fk_8681eeee397ec969 FOREIGN KEY (route_source_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8681eeee397ec969 ON swp_redirect_route (route_source_id)');
    }
}
