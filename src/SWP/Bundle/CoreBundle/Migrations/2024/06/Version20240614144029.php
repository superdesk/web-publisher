<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240614144029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE swp_tenant_domain_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_tenant_domain (id INT NOT NULL, tenant_id INT DEFAULT NULL, subdomain VARCHAR(255) DEFAULT NULL, domain_name VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22EBB6319033212A ON swp_tenant_domain (tenant_id)');
        $this->addSql('CREATE TABLE swp_failed_queue (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_21B8E166FB7336F0 ON swp_failed_queue (queue_name)');
        $this->addSql('CREATE INDEX IDX_21B8E166E3BD61CE ON swp_failed_queue (available_at)');
        $this->addSql('CREATE INDEX IDX_21B8E16616BA31DB ON swp_failed_queue (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_swp_failed_queue() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'swp_failed_queue\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON swp_failed_queue;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON swp_failed_queue FOR EACH ROW EXECUTE PROCEDURE notify_swp_failed_queue();');
        $this->addSql('ALTER TABLE swp_tenant_domain ADD CONSTRAINT FK_22EBB6319033212A FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ext_log_entries ALTER object_class TYPE VARCHAR(191)');
        $this->addSql('ALTER TABLE ext_log_entries ALTER username TYPE VARCHAR(191)');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('ALTER TABLE swp_article DROP extra');
        $this->addSql('ALTER TABLE swp_article ALTER paywall_secured DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id) WHERE deleted_at IS NULL');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id, deleted_at) WHERE deleted_at IS NOT NULL');
        $this->addSql('ALTER TABLE swp_output_channel DROP CONSTRAINT FK_AFE4D08F9033212A');
        $this->addSql('ALTER TABLE swp_output_channel ADD CONSTRAINT FK_AFE4D08F9033212A FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_package ALTER organization_id SET NOT NULL');
        $this->addSql('ALTER TABLE swp_package ALTER genre TYPE TEXT');
        $this->addSql('ALTER TABLE swp_package ALTER genre DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN swp_package.genre IS \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX swp_package_guid_idx ON swp_package (guid)');
        $this->addSql('ALTER TABLE swp_publish_destination ALTER package_guid SET NOT NULL');
        $this->addSql('ALTER TABLE swp_publish_destination ALTER paywall_secured DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_redirect_route DROP CONSTRAINT FK_8681EEEE397EC969');
        $this->addSql('DROP INDEX idx_8681eeee397ec969');
        $this->addSql('ALTER TABLE swp_redirect_route ADD CONSTRAINT FK_8681EEEE397EC969 FOREIGN KEY (route_source_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8681EEEE397EC969 ON swp_redirect_route (route_source_id)');
        $this->addSql('ALTER TABLE swp_route ALTER paywall_secured DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_route ALTER description TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE swp_route ALTER description DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_rule ALTER organization_id SET NOT NULL');
        $this->addSql('ALTER TABLE swp_user DROP enabled');
        $this->addSql('ALTER TABLE swp_user ALTER is_verified DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7384FB31F85E0677 ON swp_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7384FB31E7927C74 ON swp_user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE swp_tenant_domain_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_tenant_domain');
        $this->addSql('DROP TABLE swp_failed_queue');
        $this->addSql('ALTER TABLE ext_log_entries ALTER object_class TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ext_log_entries ALTER username TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE swp_rule ALTER organization_id DROP NOT NULL');
        $this->addSql('DROP INDEX swp_package_guid_idx');
        $this->addSql('ALTER TABLE swp_package ALTER organization_id DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_package ALTER genre TYPE TEXT');
        $this->addSql('ALTER TABLE swp_package ALTER genre DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN swp_package.genre IS NULL');
        $this->addSql('ALTER TABLE swp_route ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE swp_route ALTER description DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_route ALTER paywall_secured SET DEFAULT \'false\'');
        $this->addSql('DROP INDEX UNIQ_7384FB31F85E0677');
        $this->addSql('DROP INDEX UNIQ_7384FB31E7927C74');
        $this->addSql('ALTER TABLE swp_user ADD enabled BOOLEAN DEFAULT \'true\' NOT NULL');
        $this->addSql('ALTER TABLE swp_user ALTER is_verified SET DEFAULT \'false\'');
        $this->addSql('DROP INDEX swp_article_slug_not_null_deleted_at_idx');
        $this->addSql('DROP INDEX swp_article_slug_null_deleted_at_idx');
        $this->addSql('ALTER TABLE swp_article ADD extra TEXT DEFAULT \'a:0:{}\'');
        $this->addSql('ALTER TABLE swp_article ALTER paywall_secured SET DEFAULT \'false\'');
        $this->addSql('COMMENT ON COLUMN swp_article.extra IS \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_not_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id, deleted_at) WHERE (deleted_at IS NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_null_deleted_at_idx ON swp_article (slug, tenant_code, organization_id) WHERE (deleted_at IS NULL)');
        $this->addSql('ALTER TABLE swp_publish_destination ALTER paywall_secured SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE swp_publish_destination ALTER package_guid DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_output_channel DROP CONSTRAINT fk_afe4d08f9033212a');
        $this->addSql('ALTER TABLE swp_output_channel ADD CONSTRAINT fk_afe4d08f9033212a FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_redirect_route DROP CONSTRAINT fk_8681eeee397ec969');
        $this->addSql('DROP INDEX UNIQ_8681EEEE397EC969');
        $this->addSql('ALTER TABLE swp_redirect_route ADD CONSTRAINT fk_8681eeee397ec969 FOREIGN KEY (route_source_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8681eeee397ec969 ON swp_redirect_route (route_source_id)');
    }
}
