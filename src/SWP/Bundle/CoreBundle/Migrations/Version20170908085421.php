<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170908085421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT FK_E38D33537294869C');
        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT FK_E38D3353953C1C61');
        $this->addSql('ALTER TABLE swp_article_sources ADD CONSTRAINT FK_E38D33537294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_sources ADD CONSTRAINT FK_E38D3353953C1C61 FOREIGN KEY (source_id) REFERENCES swp_article_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT fk_e38d33537294869c');
        $this->addSql('ALTER TABLE swp_article_sources DROP CONSTRAINT fk_e38d3353953c1c61');
        $this->addSql('ALTER TABLE swp_article_sources ADD CONSTRAINT fk_e38d33537294869c FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_article_sources ADD CONSTRAINT fk_e38d3353953c1c61 FOREIGN KEY (source_id) REFERENCES swp_article_source (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
