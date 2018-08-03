<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180803085316 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_external_data ADD package_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_external_data ADD key VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE swp_external_data ADD value VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE swp_external_data DROP data');
        $this->addSql('ALTER TABLE 
          swp_external_data 
        ADD 
          CONSTRAINT FK_9C055F2CF44CABFF FOREIGN KEY (package_id) REFERENCES swp_package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9C055F2CF44CABFF ON swp_external_data (package_id)');
        $this->addSql('ALTER TABLE swp_package DROP CONSTRAINT fk_277381abee57787f');
        $this->addSql('DROP INDEX uniq_277381abee57787f');
        $this->addSql('ALTER TABLE swp_package DROP external_data_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_external_data DROP CONSTRAINT FK_9C055F2CF44CABFF');
        $this->addSql('DROP INDEX IDX_9C055F2CF44CABFF');
        $this->addSql('ALTER TABLE swp_external_data ADD data TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE swp_external_data DROP package_id');
        $this->addSql('ALTER TABLE swp_external_data DROP key');
        $this->addSql('ALTER TABLE swp_external_data DROP value');
        $this->addSql('COMMENT ON COLUMN swp_external_data.data IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_package ADD external_data_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE 
          swp_package 
        ADD 
          CONSTRAINT fk_277381abee57787f FOREIGN KEY (external_data_id) REFERENCES swp_external_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_277381abee57787f ON swp_package (external_data_id)');
    }
}
