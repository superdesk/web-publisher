<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170908083733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_event_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_event_date_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_event_location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_event_occur_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE swp_event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_event_category (id INT NOT NULL, event_id INT NOT NULL, name VARCHAR(255) NOT NULL, qcode VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_94BF293F71F7E88B ON swp_event_category (event_id)');
        $this->addSql('CREATE TABLE swp_event_date (id INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tz VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE swp_event_location (id INT NOT NULL, event_id INT NOT NULL, name VARCHAR(255) NOT NULL, qcode VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CC6DB93571F7E88B ON swp_event_location (event_id)');
        $this->addSql('CREATE TABLE swp_event_occur_status (id INT NOT NULL, name VARCHAR(255) NOT NULL, qcode VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE swp_event (id INT NOT NULL, dates_id INT DEFAULT NULL, occur_status_id INT DEFAULT NULL, etag VARCHAR(255) NOT NULL, guid VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, version INT DEFAULT NULL, ingest_id VARCHAR(255) DEFAULT NULL, recurrence_id VARCHAR(255) DEFAULT NULL, original_creator VARCHAR(255) NOT NULL, version_creator VARCHAR(255) DEFAULT NULL, ingest_provider VARCHAR(255) DEFAULT NULL, original_source VARCHAR(255) DEFAULT NULL, ingest_provider_sequence VARCHAR(255) DEFAULT NULL, definition_short VARCHAR(255) DEFAULT NULL, definition_long VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_658EE4843DA992C3 ON swp_event (dates_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_658EE4845DA64DFB ON swp_event (occur_status_id)');
        $this->addSql('ALTER TABLE swp_event_category ADD CONSTRAINT FK_94BF293F71F7E88B FOREIGN KEY (event_id) REFERENCES swp_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_event_location ADD CONSTRAINT FK_CC6DB93571F7E88B FOREIGN KEY (event_id) REFERENCES swp_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_event ADD CONSTRAINT FK_658EE4843DA992C3 FOREIGN KEY (dates_id) REFERENCES swp_event_date (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_event ADD CONSTRAINT FK_658EE4845DA64DFB FOREIGN KEY (occur_status_id) REFERENCES swp_event_occur_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_event DROP CONSTRAINT FK_658EE4843DA992C3');
        $this->addSql('ALTER TABLE swp_event DROP CONSTRAINT FK_658EE4845DA64DFB');
        $this->addSql('ALTER TABLE swp_event_category DROP CONSTRAINT FK_94BF293F71F7E88B');
        $this->addSql('ALTER TABLE swp_event_location DROP CONSTRAINT FK_CC6DB93571F7E88B');
        $this->addSql('DROP SEQUENCE swp_event_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_event_date_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_event_location_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_event_occur_status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE swp_event_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_event_category');
        $this->addSql('DROP TABLE swp_event_date');
        $this->addSql('DROP TABLE swp_event_location');
        $this->addSql('DROP TABLE swp_event_occur_status');
        $this->addSql('DROP TABLE swp_event');
    }
}
