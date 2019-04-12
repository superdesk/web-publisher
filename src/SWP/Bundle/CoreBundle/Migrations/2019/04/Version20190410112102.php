<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410112102 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_container_widgets DROP CONSTRAINT fk_ec017811fbe885e2');
        $this->addSql('ALTER TABLE swp_container_widgets DROP CONSTRAINT fk_ec017811bc21f742');
        $this->addSql('ALTER TABLE swp_container_data DROP CONSTRAINT fk_f117cac2bc21f742');
        $this->addSql('ALTER TABLE swp_container DROP CONSTRAINT fk_cf0e49301dfa7c8f');
        $this->addSql('ALTER TABLE swp_revision_log DROP CONSTRAINT fk_a1f96afd9ac03385');
        $this->addSql('ALTER TABLE swp_revision_log DROP CONSTRAINT fk_a1f96afd21852c2f');
        $this->addSql('ALTER TABLE swp_revision DROP CONSTRAINT fk_acfb1381af3077e5');
        $this->addSql('DROP SEQUENCE swp_container_data_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_container_widgets_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_container_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_widget_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_revision_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_revision_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_container_widgets');
        $this->addSql('DROP TABLE swp_widget');
        $this->addSql('DROP TABLE swp_container_data');
        $this->addSql('DROP TABLE swp_container');
        $this->addSql('DROP TABLE swp_revision_log');
        $this->addSql('DROP TABLE swp_revision');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_container_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_container_widgets_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_container_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_widget_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_revision_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_revision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_container_widgets (
          id SERIAL NOT NULL, 
          widget_id INT DEFAULT NULL, 
          container_id INT DEFAULT NULL, 
          "position" INT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_ec017811bc21f742 ON swp_container_widgets (container_id)');
        $this->addSql('CREATE INDEX idx_ec017811fbe885e2 ON swp_container_widgets (widget_id)');
        $this->addSql('CREATE TABLE swp_widget (
          id SERIAL NOT NULL, 
          name VARCHAR(255) NOT NULL, 
          type VARCHAR(255) NOT NULL, 
          visible BOOLEAN NOT NULL, 
          parameters JSON NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'2019-04-04 12:39:46.1875\' NOT NULL, 
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          tenant_code VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX swp_widget_name_idx ON swp_widget (name, tenant_code)');
        $this->addSql('COMMENT ON COLUMN swp_widget.parameters IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE swp_container_data (
          id SERIAL NOT NULL, 
          container_id INT DEFAULT NULL, 
          key VARCHAR(255) NOT NULL, 
          value VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_f117cac2bc21f742 ON swp_container_data (container_id)');
        $this->addSql('CREATE TABLE swp_container (
          id SERIAL NOT NULL, 
          revision_id INT DEFAULT NULL, 
          name VARCHAR(255) NOT NULL, 
          type INT NOT NULL, 
          styles TEXT DEFAULT NULL, 
          css_class VARCHAR(255) DEFAULT NULL, 
          visible BOOLEAN NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'2019-04-04 12:39:46.184232\' NOT NULL, 
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          tenant_code VARCHAR(255) NOT NULL, 
          uuid VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_cf0e49301dfa7c8f ON swp_container (revision_id)');
        $this->addSql('CREATE UNIQUE INDEX swp_name_idx ON swp_container (name, tenant_code, revision_id)');
        $this->addSql('CREATE TABLE swp_revision_log (
          id INT NOT NULL, 
          target_revision_id INT DEFAULT NULL, 
          source_revision_id INT DEFAULT NULL, 
          object_type VARCHAR(255) NOT NULL, 
          object_id INT NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          event VARCHAR(255) NOT NULL, 
          tenant_code VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_a1f96afd9ac03385 ON swp_revision_log (target_revision_id)');
        $this->addSql('CREATE INDEX idx_a1f96afd21852c2f ON swp_revision_log (source_revision_id)');
        $this->addSql('CREATE TABLE swp_revision (
          id INT NOT NULL, 
          previous_revision_id INT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          is_active BOOLEAN NOT NULL, 
          unique_key VARCHAR(255) NOT NULL, 
          status VARCHAR(255) NOT NULL, 
          tenant_code VARCHAR(255) NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_acfb1381af3077e5 ON swp_revision (previous_revision_id)');
        $this->addSql('ALTER TABLE 
          swp_container_widgets 
        ADD 
          CONSTRAINT fk_ec017811fbe885e2 FOREIGN KEY (widget_id) REFERENCES swp_widget (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_container_widgets 
        ADD 
          CONSTRAINT fk_ec017811bc21f742 FOREIGN KEY (container_id) REFERENCES swp_container (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_container_data 
        ADD 
          CONSTRAINT fk_f117cac2bc21f742 FOREIGN KEY (container_id) REFERENCES swp_container (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_container 
        ADD 
          CONSTRAINT fk_cf0e49301dfa7c8f FOREIGN KEY (revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_revision_log 
        ADD 
          CONSTRAINT fk_a1f96afd9ac03385 FOREIGN KEY (target_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_revision_log 
        ADD 
          CONSTRAINT fk_a1f96afd21852c2f FOREIGN KEY (source_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          swp_revision 
        ADD 
          CONSTRAINT fk_acfb1381af3077e5 FOREIGN KEY (previous_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
