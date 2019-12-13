<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191213101208 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_redirect_route_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_redirect_route (id INT NOT NULL, route_source_id INT DEFAULT NULL, route_target_id INT DEFAULT NULL, host VARCHAR(255) NOT NULL, schemes TEXT NOT NULL, methods TEXT NOT NULL, defaults TEXT NOT NULL, requirements TEXT NOT NULL, options TEXT NOT NULL, condition_expr VARCHAR(255) DEFAULT NULL, variable_pattern VARCHAR(255) DEFAULT NULL, staticPrefix VARCHAR(255) DEFAULT NULL, route_name VARCHAR(255) DEFAULT NULL, uri VARCHAR(255) DEFAULT NULL, permanent BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8681EEEE397EC969 ON swp_redirect_route (route_source_id)');
        $this->addSql('CREATE INDEX IDX_8681EEEEB9CCDE6E ON swp_redirect_route (route_target_id)');
        $this->addSql('CREATE INDEX swp_redirect_route_prefix ON swp_redirect_route (staticPrefix)');
        $this->addSql('COMMENT ON COLUMN swp_redirect_route.schemes IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_redirect_route.methods IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_redirect_route.defaults IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_redirect_route.requirements IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN swp_redirect_route.options IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_redirect_route ADD CONSTRAINT FK_8681EEEE397EC969 FOREIGN KEY (route_source_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_redirect_route ADD CONSTRAINT FK_8681EEEEB9CCDE6E FOREIGN KEY (route_target_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX prefix_idx');
        $this->addSql('CREATE UNIQUE INDEX prefix_idx ON swp_route (staticPrefix)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_redirect_route_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_redirect_route');
        $this->addSql('DROP INDEX prefix_idx');
        $this->addSql('CREATE INDEX prefix_idx ON swp_route (staticprefix)');
    }
}
