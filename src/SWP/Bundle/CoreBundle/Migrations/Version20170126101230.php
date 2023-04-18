<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170126101230 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE IF NOT EXISTS ext_log_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  request_metrics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_package_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_api_key_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_content_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_content_list_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_fbia_application_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_fbia_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_fbia_feed_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_fbia_page_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_menu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_organization_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_rule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_tenant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS  swp_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE IF NOT EXISTS ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX IF NOT EXISTS log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX IF NOT EXISTS log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX IF NOT EXISTS log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS request_metrics (id INT NOT NULL, uri TEXT NOT NULL, route VARCHAR(255) NOT NULL, duration INT NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_container_data (id SERIAL NOT NULL, container_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_F117CAC2BC21F742 ON swp_container_data (container_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_container_widgets (id SERIAL NOT NULL, widget_id INT DEFAULT NULL, container_id INT DEFAULT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_EC017811FBE885E2 ON swp_container_widgets (widget_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_EC017811BC21F742 ON swp_container_widgets (container_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_item (id INT NOT NULL, package_id INT DEFAULT NULL, headline VARCHAR(255) NOT NULL, slugline VARCHAR(255) DEFAULT NULL, guid VARCHAR(255) NOT NULL, byline VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, subjects TEXT NOT NULL, services TEXT DEFAULT NULL, keywords TEXT NOT NULL, places TEXT NOT NULL, type VARCHAR(255) NOT NULL, located VARCHAR(255) DEFAULT NULL, urgency INT NOT NULL, priority INT NOT NULL, version INT NOT NULL, body TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_E10C0866F44CABFF ON swp_item (package_id)');
        $this->addSql('COMMENT ON COLUMN swp_item.subjects IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_item.services IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_item.keywords IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_item.places IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_package (id INT NOT NULL, headline VARCHAR(255) NOT NULL, slugline VARCHAR(255) NOT NULL, guid VARCHAR(255) NOT NULL, byline VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, subjects TEXT NOT NULL, services TEXT DEFAULT NULL, keywords TEXT NOT NULL, places TEXT NOT NULL, type VARCHAR(255) NOT NULL, located VARCHAR(255) DEFAULT NULL, urgency INT NOT NULL, priority INT NOT NULL, version INT NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN swp_package.subjects IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_package.services IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_package.keywords IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_package.places IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_file (id SERIAL NOT NULL, asset_id VARCHAR(255) NOT NULL, file_extension VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_image_rendition (id SERIAL NOT NULL, image_id INT DEFAULT NULL, media_id INT DEFAULT NULL, width INT NOT NULL, height INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_932D0BFB3DA5256D ON swp_image_rendition (image_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_932D0BFBEA9FDD75 ON swp_image_rendition (media_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_api_key (id INT NOT NULL, user_id INT DEFAULT NULL, api_key VARCHAR(255) NOT NULL, valid_to TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_30090BA3A76ED395 ON swp_api_key (user_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_article (id SERIAL NOT NULL, route_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, body TEXT NOT NULL, title VARCHAR(255) NOT NULL, keywords TEXT NOT NULL, lead TEXT DEFAULT NULL, template_name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, publish_start_date DATE DEFAULT NULL, publish_end_date DATE DEFAULT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_publishable BOOLEAN NOT NULL, metadata TEXT DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_FB21E85834ECB4E6 ON swp_article (route_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS swp_article_slug_idx ON swp_article (slug, tenant_code)');
        //$this->addSql('COMMENT ON COLUMN swp_article.keywords IS \'(DC2Type:array)\''); //column shoudn't exist
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_article_media (id SERIAL NOT NULL, article_id INT DEFAULT NULL, file_id INT DEFAULT NULL, image_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, body TEXT DEFAULT NULL, description TEXT DEFAULT NULL, located VARCHAR(255) DEFAULT NULL, by_line VARCHAR(255) DEFAULT NULL, mimetype VARCHAR(255) NOT NULL, usage_terms VARCHAR(255) DEFAULT NULL, created_at DATE NOT NULL, updated_at DATE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_B9721F7E7294869C ON swp_article_media (article_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_B9721F7E93CB796C ON swp_article_media (file_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_B9721F7E3DA5256D ON swp_article_media (image_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_container (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, type INT NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, styles TEXT DEFAULT NULL, css_class VARCHAR(255) DEFAULT NULL, visible BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS swp_name_idx ON swp_container (name, tenant_code)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_content_list (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, cache_life_time INT DEFAULT NULL, list_limit INT DEFAULT NULL, filters TEXT NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN swp_content_list.filters IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_content_list_item (id INT NOT NULL, content_id INT NOT NULL, content_list_id INT NOT NULL, position INT NOT NULL, enabled BOOLEAN NOT NULL, sticky BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_8513AA6984A0A3ED ON swp_content_list_item (content_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_8513AA69E2A6CF38 ON swp_content_list_item (content_list_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_fbia_application (id INT NOT NULL, app_id VARCHAR(255) NOT NULL, app_secret VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_fbia_article (id INT NOT NULL, article_id INT NOT NULL, feed_id INT NOT NULL, tenant_code VARCHAR(255) NOT NULL, submission_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, errors TEXT DEFAULT \'{}\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_F9B0A4377294869C ON swp_fbia_article (article_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_F9B0A43751A5BC03 ON swp_fbia_article (feed_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS swp_fbia_article_idx ON swp_fbia_article (submission_id, feed_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_fbia_feed (id INT NOT NULL, facebook_page_id INT NOT NULL, content_list_id INT NOT NULL, tenant_code VARCHAR(255) NOT NULL, mode INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_36D3F8097A7D7F9F ON swp_fbia_feed (facebook_page_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_36D3F809E2A6CF38 ON swp_fbia_feed (content_list_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS swp_fbia_feed_idx ON swp_fbia_feed (content_list_id, facebook_page_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_fbia_page (id INT NOT NULL, application_id INT DEFAULT NULL, page_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, access_token VARCHAR(255) DEFAULT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_1990A823E030ACD ON swp_fbia_page (application_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_image (id SERIAL NOT NULL, asset_id VARCHAR(255) NOT NULL, file_extension VARCHAR(255) NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, created_at DATE NOT NULL, updated_at DATE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_menu (id INT NOT NULL, root_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, route_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) DEFAULT NULL, link_attributes TEXT NOT NULL, children_attributes TEXT NOT NULL, label_attributes TEXT NOT NULL, uri VARCHAR(255) DEFAULT NULL, attributes TEXT NOT NULL, extras TEXT NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, level INT NOT NULL, position INT NOT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_831217EB79066886 ON swp_menu (root_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_831217EB727ACA70 ON swp_menu (parent_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_831217EB34ECB4E6 ON swp_menu (route_id)');
        $this->addSql('COMMENT ON COLUMN swp_menu.link_attributes IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_menu.children_attributes IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_menu.label_attributes IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_menu.attributes IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_menu.extras IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_organization (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_F9E66D1A77153098 ON swp_organization (code)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_route (id SERIAL NOT NULL, root_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, route_content_id INT DEFAULT NULL, host VARCHAR(255) NOT NULL, schemes TEXT NOT NULL, methods TEXT NOT NULL, defaults TEXT NOT NULL, requirements TEXT NOT NULL, options TEXT NOT NULL, condition_expr VARCHAR(255) DEFAULT NULL, variable_pattern VARCHAR(255) DEFAULT NULL, staticPrefix VARCHAR(255) DEFAULT NULL, template_name VARCHAR(255) DEFAULT NULL, articles_template_name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, cache_time_in_seconds INT NOT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, level INT NOT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        //$this->addSql('CREATE INDEX IF NOT EXISTS IDX_5CE4CE5A79066886 ON swp_route (root_id)'); //column shouldn't exist
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_5CE4CE5A727ACA70 ON swp_route (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_5CE4CE5A8AD7690A ON swp_route (route_content_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS prefix_idx ON swp_route (staticPrefix)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS swp_route_name_idx ON swp_route (name, tenant_code)');
        $this->addSql('COMMENT ON COLUMN swp_route.schemes IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_route.methods IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_route.defaults IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_route.requirements IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_route.options IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_rule (id INT NOT NULL, expression VARCHAR(255) NOT NULL, priority INT NOT NULL, configuration TEXT NOT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN swp_rule.configuration IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_tenant (id INT NOT NULL, organization_id INT DEFAULT NULL, tenant_route_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subdomain VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, theme_name VARCHAR(255) DEFAULT NULL, domain_name VARCHAR(255) DEFAULT NULL, amp_enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_EC6095FE5E237E06 ON swp_tenant (name)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_EC6095FEC1D5962E ON swp_tenant (subdomain)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_EC6095FE77153098 ON swp_tenant (code)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_EC6095FE32C8A3DE ON swp_tenant (organization_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_EC6095FE154AD611 ON swp_tenant (tenant_route_id)');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_user (id INT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, tenant_code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_7384FB3192FC23A8 ON swp_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_7384FB31A0D96FBF ON swp_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_7384FB31C05FB297 ON swp_user (confirmation_token)');
        $this->addSql('COMMENT ON COLUMN swp_user.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE IF NOT EXISTS swp_widget (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, visible BOOLEAN NOT NULL, parameters TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'now\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_27C04F4C5E237E06 ON swp_widget (name)');
        $this->addSql('COMMENT ON COLUMN swp_widget.parameters IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE swp_container_data ADD CONSTRAINT FK_F117CAC2BC21F742 FOREIGN KEY (container_id) REFERENCES swp_container (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_container_widgets ADD CONSTRAINT FK_EC017811FBE885E2 FOREIGN KEY (widget_id) REFERENCES swp_widget (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_container_widgets ADD CONSTRAINT FK_EC017811BC21F742 FOREIGN KEY (container_id) REFERENCES swp_container (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_item ADD CONSTRAINT FK_E10C0866F44CABFF FOREIGN KEY (package_id) REFERENCES swp_package (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_image_rendition ADD CONSTRAINT FK_932D0BFB3DA5256D FOREIGN KEY (image_id) REFERENCES swp_image (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_image_rendition ADD CONSTRAINT FK_932D0BFBEA9FDD75 FOREIGN KEY (media_id) REFERENCES swp_article_media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_api_key ADD CONSTRAINT FK_30090BA3A76ED395 FOREIGN KEY (user_id) REFERENCES swp_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E85834ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_media ADD CONSTRAINT FK_B9721F7E7294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_media ADD CONSTRAINT FK_B9721F7E93CB796C FOREIGN KEY (file_id) REFERENCES swp_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_media ADD CONSTRAINT FK_B9721F7E3DA5256D FOREIGN KEY (image_id) REFERENCES swp_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_content_list_item ADD CONSTRAINT FK_8513AA6984A0A3ED FOREIGN KEY (content_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_content_list_item ADD CONSTRAINT FK_8513AA69E2A6CF38 FOREIGN KEY (content_list_id) REFERENCES swp_content_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_fbia_article ADD CONSTRAINT FK_F9B0A4377294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_fbia_article ADD CONSTRAINT FK_F9B0A43751A5BC03 FOREIGN KEY (feed_id) REFERENCES swp_fbia_feed (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_fbia_feed ADD CONSTRAINT FK_36D3F8097A7D7F9F FOREIGN KEY (facebook_page_id) REFERENCES swp_fbia_page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_fbia_feed ADD CONSTRAINT FK_36D3F809E2A6CF38 FOREIGN KEY (content_list_id) REFERENCES swp_content_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_fbia_page ADD CONSTRAINT FK_1990A823E030ACD FOREIGN KEY (application_id) REFERENCES swp_fbia_application (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_menu ADD CONSTRAINT FK_831217EB79066886 FOREIGN KEY (root_id) REFERENCES swp_menu (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_menu ADD CONSTRAINT FK_831217EB727ACA70 FOREIGN KEY (parent_id) REFERENCES swp_menu (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_menu ADD CONSTRAINT FK_831217EB34ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_route ADD CONSTRAINT FK_5CE4CE5A79066886 FOREIGN KEY (root_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_route ADD CONSTRAINT FK_5CE4CE5A727ACA70 FOREIGN KEY (parent_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_route ADD CONSTRAINT FK_5CE4CE5A8AD7690A FOREIGN KEY (route_content_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_tenant ADD CONSTRAINT FK_EC6095FE32C8A3DE FOREIGN KEY (organization_id) REFERENCES swp_organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_tenant ADD CONSTRAINT FK_EC6095FE154AD611 FOREIGN KEY (tenant_route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE swp_item DROP CONSTRAINT FK_E10C0866F44CABFF');
        $this->addSql('ALTER TABLE swp_article_media DROP CONSTRAINT FK_B9721F7E93CB796C');
        $this->addSql('ALTER TABLE swp_article_media DROP CONSTRAINT FK_B9721F7E7294869C');
        $this->addSql('ALTER TABLE swp_content_list_item DROP CONSTRAINT FK_8513AA6984A0A3ED');
        $this->addSql('ALTER TABLE swp_fbia_article DROP CONSTRAINT FK_F9B0A4377294869C');
        $this->addSql('ALTER TABLE swp_route DROP CONSTRAINT FK_5CE4CE5A8AD7690A');
        $this->addSql('ALTER TABLE swp_image_rendition DROP CONSTRAINT FK_932D0BFBEA9FDD75');
        $this->addSql('ALTER TABLE swp_container_data DROP CONSTRAINT FK_F117CAC2BC21F742');
        $this->addSql('ALTER TABLE swp_container_widgets DROP CONSTRAINT FK_EC017811BC21F742');
        $this->addSql('ALTER TABLE swp_content_list_item DROP CONSTRAINT FK_8513AA69E2A6CF38');
        $this->addSql('ALTER TABLE swp_fbia_feed DROP CONSTRAINT FK_36D3F809E2A6CF38');
        $this->addSql('ALTER TABLE swp_fbia_page DROP CONSTRAINT FK_1990A823E030ACD');
        $this->addSql('ALTER TABLE swp_fbia_article DROP CONSTRAINT FK_F9B0A43751A5BC03');
        $this->addSql('ALTER TABLE swp_fbia_feed DROP CONSTRAINT FK_36D3F8097A7D7F9F');
        $this->addSql('ALTER TABLE swp_image_rendition DROP CONSTRAINT FK_932D0BFB3DA5256D');
        $this->addSql('ALTER TABLE swp_article_media DROP CONSTRAINT FK_B9721F7E3DA5256D');
        $this->addSql('ALTER TABLE swp_menu DROP CONSTRAINT FK_831217EB79066886');
        $this->addSql('ALTER TABLE swp_menu DROP CONSTRAINT FK_831217EB727ACA70');
        $this->addSql('ALTER TABLE swp_tenant DROP CONSTRAINT FK_EC6095FE32C8A3DE');
        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E85834ECB4E6');
        $this->addSql('ALTER TABLE swp_menu DROP CONSTRAINT FK_831217EB34ECB4E6');
        $this->addSql('ALTER TABLE swp_route DROP CONSTRAINT FK_5CE4CE5A79066886');
        $this->addSql('ALTER TABLE swp_route DROP CONSTRAINT FK_5CE4CE5A727ACA70');
        $this->addSql('ALTER TABLE swp_tenant DROP CONSTRAINT FK_EC6095FE154AD611');
        $this->addSql('ALTER TABLE swp_api_key DROP CONSTRAINT FK_30090BA3A76ED395');
        $this->addSql('ALTER TABLE swp_container_widgets DROP CONSTRAINT FK_EC017811FBE885E2');
        $this->addSql('DROP SEQUENCE ext_log_entries_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE request_metrics_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_package_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_api_key_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_content_list_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_content_list_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_fbia_application_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_fbia_article_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_fbia_feed_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_fbia_page_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_menu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_organization_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_rule_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_tenant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_user_id_seq CASCADE');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE request_metrics');
        $this->addSql('DROP TABLE swp_container_data');
        $this->addSql('DROP TABLE swp_container_widgets');
        $this->addSql('DROP TABLE swp_item');
        $this->addSql('DROP TABLE swp_package');
        $this->addSql('DROP TABLE swp_file');
        $this->addSql('DROP TABLE swp_image_rendition');
        $this->addSql('DROP TABLE swp_api_key');
        $this->addSql('DROP TABLE swp_article');
        $this->addSql('DROP TABLE swp_article_media');
        $this->addSql('DROP TABLE swp_container');
        $this->addSql('DROP TABLE swp_content_list');
        $this->addSql('DROP TABLE swp_content_list_item');
        $this->addSql('DROP TABLE swp_fbia_application');
        $this->addSql('DROP TABLE swp_fbia_article');
        $this->addSql('DROP TABLE swp_fbia_feed');
        $this->addSql('DROP TABLE swp_fbia_page');
        $this->addSql('DROP TABLE swp_image');
        $this->addSql('DROP TABLE swp_menu');
        $this->addSql('DROP TABLE swp_organization');
        $this->addSql('DROP TABLE swp_route');
        $this->addSql('DROP TABLE swp_rule');
        $this->addSql('DROP TABLE swp_tenant');
        $this->addSql('DROP TABLE swp_user');
        $this->addSql('DROP TABLE swp_widget');
    }
}
