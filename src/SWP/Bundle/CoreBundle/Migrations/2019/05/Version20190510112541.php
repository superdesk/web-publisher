<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190510112541 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_preview_token DROP CONSTRAINT FK_AD1CA87234ECB4E6');
        $this->addSql('ALTER TABLE swp_package_preview_token ADD CONSTRAINT FK_AD1CA87234ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_publish_destination DROP CONSTRAINT FK_8C71004A34ECB4E6');
        $this->addSql('ALTER TABLE swp_publish_destination ADD CONSTRAINT FK_8C71004A34ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_preview_token DROP CONSTRAINT fk_ad1ca87234ecb4e6');
        $this->addSql('ALTER TABLE swp_package_preview_token ADD CONSTRAINT fk_ad1ca87234ecb4e6 FOREIGN KEY (route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_publish_destination DROP CONSTRAINT fk_8c71004a34ecb4e6');
        $this->addSql('ALTER TABLE swp_publish_destination ADD CONSTRAINT fk_8c71004a34ecb4e6 FOREIGN KEY (route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
