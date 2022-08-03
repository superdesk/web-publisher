<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170427114227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_item_renditions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_item_renditions (id INT NOT NULL, item_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, href VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, mimetype VARCHAR(255) DEFAULT NULL, media VARCHAR(255) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C89753FD126F525E ON swp_item_renditions (item_id)');
        $this->addSql('ALTER TABLE swp_item_renditions ADD CONSTRAINT FK_C89753FD126F525E FOREIGN KEY (item_id) REFERENCES swp_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_item ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE swp_item SET pub_status = \'usable\' WHERE pub_status IS NULL');
        $this->addSql('ALTER TABLE swp_item ALTER pub_status SET NOT NULL');
        $this->addSql('ALTER TABLE swp_article ADD package_id INT DEFAULT NULL');
        $this->addSql('UPDATE swp_article SET package_id = (SELECT distinct on (p.guid) p.id FROM swp_package AS p WHERE code = p.guid)');
        $this->addSql('ALTER TABLE swp_article ALTER keywords SET NOT NULL');
        $this->addSql('ALTER TABLE swp_article ALTER tenant_code SET NOT NULL');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E858F44CABFF FOREIGN KEY (package_id) REFERENCES swp_package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FB21E858F44CABFF ON swp_article (package_id)');
        $this->addSql('ALTER TABLE swp_article_media DROP tenant_code');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'now\'');
        $this->addSql('ALTER TABLE swp_container ALTER uuid DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_image DROP tenant_code');
        $this->addSql('ALTER TABLE swp_package ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE swp_package SET organization_id = (SELECT t.id FROM swp_organization AS t LIMIT 1)');
        $this->addSql('ALTER TABLE swp_package ALTER organization_id DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_package ADD body TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_package ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE swp_package SET status = \'new\' WHERE (SELECT COUNT(a.id) FROM swp_article AS a WHERE a.package_id = id) = 0');
        $this->addSql('UPDATE swp_package SET status = \'published\' WHERE (SELECT COUNT(a.id) FROM swp_article AS a WHERE a.package_id = id AND a.status = \'published\') > 0');
        $this->addSql('UPDATE swp_package SET status = \'unpublished\' WHERE (SELECT COUNT(a.id) FROM swp_article AS a WHERE a.package_id = id AND a.status = \'unpublished\') > 0');
        $this->addSql('ALTER TABLE swp_package ALTER status SET NOT NULL');
        $this->addSql('ALTER TABLE swp_package ALTER slugline DROP NOT NULL');
        $this->addSql('UPDATE swp_package SET pub_status = \'usable\' WHERE pub_status IS NULL');
        $this->addSql('ALTER TABLE swp_package ALTER pub_status SET NOT NULL');
        $this->addSql('ALTER TABLE swp_package ADD CONSTRAINT FK_277381AB32C8A3DE FOREIGN KEY (organization_id) REFERENCES swp_organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_277381AB32C8A3DE ON swp_package (organization_id)');
        $this->addSql('ALTER TABLE swp_rule ADD COLUMN organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE swp_rule SET organization_id = (SELECT t.organization_id FROM swp_tenant AS t WHERE tenant_code = t.code)');
        $this->addSql('ALTER TABLE swp_rule ALTER organization_id DROP DEFAULT');
        $this->addSql('ALTER TABLE swp_rule ALTER tenant_code DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_rule ADD CONSTRAINT FK_B8CF81B432C8A3DE FOREIGN KEY (organization_id) REFERENCES swp_organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B8CF81B432C8A3DE ON swp_rule (organization_id)');
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'now\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_item_renditions_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_item_renditions');
        $this->addSql('ALTER TABLE swp_rule DROP CONSTRAINT FK_B8CF81B432C8A3DE');
        $this->addSql('DROP INDEX IDX_B8CF81B432C8A3DE');
        $this->addSql('ALTER TABLE swp_rule DROP organization_id');
        $this->addSql('ALTER TABLE swp_rule ALTER tenant_code SET NOT NULL');
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'2017-04-27 13:41:56.106694\'');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'2017-04-27 13:41:56.106694\'');
        $this->addSql('ALTER TABLE swp_container ALTER uuid SET DEFAULT \'substr(md5((random())::text), 0, 12)\'');
        $this->addSql('ALTER TABLE swp_image ADD tenant_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E858F44CABFF');
        $this->addSql('DROP INDEX IDX_FB21E858F44CABFF');
        $this->addSql('ALTER TABLE swp_article DROP package_id');
        $this->addSql('ALTER TABLE swp_article ALTER keywords DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_article ALTER tenant_code DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_article_media ADD tenant_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE swp_item DROP name');
        $this->addSql('ALTER TABLE swp_item ALTER pub_status DROP NOT NULL');
        $this->addSql('ALTER TABLE swp_package DROP CONSTRAINT FK_277381AB32C8A3DE');
        $this->addSql('DROP INDEX IDX_277381AB32C8A3DE');
        $this->addSql('ALTER TABLE swp_package DROP organization_id');
        $this->addSql('ALTER TABLE swp_package DROP body');
        $this->addSql('ALTER TABLE swp_package DROP status');
        $this->addSql('ALTER TABLE swp_package ALTER slugline SET NOT NULL');
        $this->addSql('ALTER TABLE swp_package ALTER pub_status DROP NOT NULL');
    }
}
