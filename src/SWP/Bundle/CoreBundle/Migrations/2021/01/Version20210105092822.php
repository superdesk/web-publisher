<?php declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105092822 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_failed_queue_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_failed_queue');
        $this->addSql('DROP INDEX uniq_7384fb31c05fb297');
        $this->addSql('DROP INDEX uniq_7384fb3192fc23a8');
        $this->addSql('DROP INDEX uniq_7384fb31a0d96fbf');
        $this->addSql('ALTER TABLE swp_user DROP username_canonical');
        $this->addSql('ALTER TABLE swp_user DROP email_canonical');
        $this->addSql('ALTER TABLE swp_user DROP enabled');
        $this->addSql('ALTER TABLE swp_user DROP confirmation_token');
        $this->addSql('ALTER TABLE swp_user DROP salt');
        $this->addSql('ALTER TABLE swp_user DROP password_requested_at');
        $this->addSql('ALTER TABLE swp_user DROP last_login');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7384FB31F85E0677 ON swp_user (username)');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id) WHERE deleted_at IS NULL');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id, deleted_at) WHERE deleted_at IS NOT NULL');
        $this->addSql('ALTER TABLE swp_package_author DROP CONSTRAINT FK_574B723AF675F31B');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT FK_574B723AF675F31B FOREIGN KEY (author_id) REFERENCES swp_author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE swp_failed_queue_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_failed_queue (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_21b8e16616ba31db ON swp_failed_queue (delivered_at)');
        $this->addSql('CREATE INDEX idx_21b8e166fb7336f0 ON swp_failed_queue (queue_name)');
        $this->addSql('CREATE INDEX idx_21b8e166e3bd61ce ON swp_failed_queue (available_at)');
        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id, deleted_at) WHERE (deleted_at IS NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id) WHERE (deleted_at IS NULL)');
        $this->addSql('ALTER TABLE swp_package_author DROP CONSTRAINT fk_574b723af675f31b');
        $this->addSql('ALTER TABLE swp_package_author ADD CONSTRAINT fk_574b723af675f31b FOREIGN KEY (author_id) REFERENCES swp_author (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX UNIQ_7384FB31F85E0677');
        $this->addSql('ALTER TABLE swp_user ADD username_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD email_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD enabled BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE swp_user ADD confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_user ADD last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_7384fb31c05fb297 ON swp_user (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7384fb3192fc23a8 ON swp_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7384fb31a0d96fbf ON swp_user (email_canonical)');
    }
}
