<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170217123721 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_revision_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_revision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_revision_log (id INT NOT NULL, target_revision_id INT DEFAULT NULL, source_revision_id INT DEFAULT NULL, object_type VARCHAR(255) NOT NULL, object_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, event VARCHAR(255) NOT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A1F96AFD9AC03385 ON swp_revision_log (target_revision_id)');
        $this->addSql('CREATE INDEX IDX_A1F96AFD21852C2F ON swp_revision_log (source_revision_id)');
        $this->addSql('CREATE TABLE swp_revision (id INT NOT NULL, previous_revision_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, unique_key VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, tenant_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ACFB1381AF3077E5 ON swp_revision (previous_revision_id)');
        $this->addSql('ALTER TABLE swp_revision_log ADD CONSTRAINT FK_A1F96AFD9AC03385 FOREIGN KEY (target_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_revision_log ADD CONSTRAINT FK_A1F96AFD21852C2F FOREIGN KEY (source_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_revision ADD CONSTRAINT FK_ACFB1381AF3077E5 FOREIGN KEY (previous_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article ALTER keywords DROP NOT NULL');
        $this->addSql('DROP INDEX swp_name_idx');
        $this->addSql('ALTER TABLE swp_container ADD revision_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_container ADD uuid VARCHAR(255) NOT NULL DEFAULT substr(md5(random()::text), 0, 12);');
        $this->addSql('ALTER TABLE swp_container DROP width');
        $this->addSql('ALTER TABLE swp_container DROP height');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'now\'');
        $this->addSql('ALTER TABLE swp_container ADD CONSTRAINT FK_CF0E49301DFA7C8F FOREIGN KEY (revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CF0E49301DFA7C8F ON swp_container (revision_id)');
        $this->addSql('CREATE UNIQUE INDEX swp_name_idx ON swp_container (name, tenant_code, revision_id)');
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'now\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_revision_log DROP CONSTRAINT FK_A1F96AFD9AC03385');
        $this->addSql('ALTER TABLE swp_revision_log DROP CONSTRAINT FK_A1F96AFD21852C2F');
        $this->addSql('ALTER TABLE swp_container DROP CONSTRAINT FK_CF0E49301DFA7C8F');
        $this->addSql('ALTER TABLE swp_revision DROP CONSTRAINT FK_ACFB1381AF3077E5');
        $this->addSql('DROP SEQUENCE swp_revision_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_revision_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_revision_log');
        $this->addSql('DROP TABLE swp_revision');
        $this->addSql('ALTER TABLE swp_widget ALTER created_at SET DEFAULT \'2017-01-26 14:48:07.617947\'');
        $this->addSql('DROP INDEX IDX_CF0E49301DFA7C8F');
        $this->addSql('DROP INDEX swp_name_idx');
        $this->addSql('ALTER TABLE swp_container ADD height INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_container DROP uuid');
        $this->addSql('ALTER TABLE swp_container ALTER created_at SET DEFAULT \'2017-01-26 14:48:07.617947\'');
        $this->addSql('ALTER TABLE swp_container RENAME COLUMN revision_id TO width');
        $this->addSql('CREATE UNIQUE INDEX swp_name_idx ON swp_container (name, tenant_code)');
        $this->addSql('ALTER TABLE swp_article ALTER keywords SET NOT NULL');
    }
}
