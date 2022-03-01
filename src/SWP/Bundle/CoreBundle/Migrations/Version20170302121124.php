<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170302121124 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX swp_article_slug_idx');
        $this->addSql('ALTER TABLE swp_article ADD organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_article ADD CONSTRAINT FK_FB21E85832C8A3DE FOREIGN KEY (organization_id) REFERENCES swp_organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FB21E85832C8A3DE ON swp_article (organization_id)');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_idx ON swp_article (slug, tenant_code, organization_id)');
        $this->addSql('ALTER TABLE swp_article ALTER tenant_code DROP NOT NULL');
        $this->addSql('UPDATE swp_article SET organization_id = (SELECT t.organization_id FROM swp_tenant AS t WHERE tenant_code = t.code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article DROP CONSTRAINT FK_FB21E85832C8A3DE');
        $this->addSql('DROP INDEX IDX_FB21E85832C8A3DE');
        $this->addSql('DROP INDEX swp_article_slug_idx');
        $this->addSql('ALTER TABLE swp_article DROP organization_id');
        $this->addSql('CREATE UNIQUE INDEX swp_article_slug_idx ON swp_article (slug, tenant_code)');
        $this->addSql('ALTER TABLE swp_article ALTER tenant_code SET NOT NULL');
    }
}
