<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210119142827 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_user_reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_user_reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_53CA7BFAA76ED395 ON swp_user_reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN swp_user_reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN swp_user_reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE swp_user_reset_password_request ADD CONSTRAINT FK_53CA7BFAA76ED395 FOREIGN KEY (user_id) REFERENCES swp_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX uniq_7384fb3192fc23a8');
        $this->addSql('DROP INDEX uniq_7384fb31a0d96fbf');
        $this->addSql('ALTER TABLE swp_user DROP username_canonical');
        $this->addSql('ALTER TABLE swp_user DROP email_canonical');
        $this->addSql('ALTER TABLE swp_user DROP salt');
        $this->addSql('ALTER TABLE swp_user DROP last_login');
        $this->addSql('ALTER TABLE swp_user DROP password_requested_at');
//        if($schema->getTable('swp_user')->hasColumn('enabled')) {
//            $this->addSql('ALTER TABLE swp_user RENAME COLUMN enabled TO is_verified');
//        }
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7384FB31F85E0677 ON swp_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7384FB31E7927C74 ON swp_user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_user_reset_password_request_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_user_reset_password_request');
        $this->addSql('DROP INDEX UNIQ_7384FB31F85E0677');
        $this->addSql('DROP INDEX UNIQ_7384FB31E7927C74');
        $this->addSql('ALTER TABLE swp_user ADD username_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD email_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        if($schema->getTable('swp_user')->hasColumn('is_verified')) {
            $this->addSql('ALTER TABLE swp_user RENAME COLUMN is_verified TO enabled');
        }
        $this->addSql('CREATE UNIQUE INDEX uniq_7384fb3192fc23a8 ON swp_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7384fb31a0d96fbf ON swp_user (email_canonical)');
    }
}
