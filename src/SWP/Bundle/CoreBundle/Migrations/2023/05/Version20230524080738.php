<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230524080738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user_reset_password_table again';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE IF NOT EXISTS swp_user_reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_user_reset_password_request (
                id INT NOT NULL,
                user_id INT NOT NULL,
                selector VARCHAR(20) NOT NULL,
                hashed_token VARCHAR(100) NOT NULL,
                requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )'
        );
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_53CA7BFAA76ED395 ON swp_user_reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN swp_user_reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN swp_user_reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE swp_user_reset_password_request DROP CONSTRAINT IF EXISTS FK_53CA7BFAA76ED395');
        $this->addSql('ALTER TABLE swp_user_reset_password_request ADD CONSTRAINT FK_53CA7BFAA76ED395 FOREIGN KEY (user_id) REFERENCES swp_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_user_reset_password_request_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_user_reset_password_request');
    }
}
