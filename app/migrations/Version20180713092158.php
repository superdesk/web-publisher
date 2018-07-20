<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180713092158 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_external_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_external_data (
          id INT NOT NULL, 
          data TEXT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN swp_external_data.data IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_package ADD external_data_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE 
          swp_package 
        ADD 
          CONSTRAINT FK_277381ABEE57787F FOREIGN KEY (external_data_id) REFERENCES swp_external_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_277381ABEE57787F ON swp_package (external_data_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package DROP CONSTRAINT FK_277381ABEE57787F');
        $this->addSql('DROP SEQUENCE swp_external_data_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_external_data');
        $this->addSql('DROP INDEX UNIQ_277381ABEE57787F');
        $this->addSql('ALTER TABLE swp_package DROP external_data_id');
    }
}
