<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201214123538 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_author DROP CONSTRAINT FK_574B723AF675F31B');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT FK_574B723AF675F31B FOREIGN KEY (author_id) REFERENCES swp_author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_author DROP CONSTRAINT fk_574b723af675f31b');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT fk_574b723af675f31b FOREIGN KEY (author_id) REFERENCES swp_author (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
