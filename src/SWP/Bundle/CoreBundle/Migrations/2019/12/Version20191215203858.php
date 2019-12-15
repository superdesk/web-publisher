<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191215203858 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_preview_token DROP CONSTRAINT FK_AD1CA87234ECB4E6');
        $this->addSql('ALTER TABLE 
          swp_package_preview_token 
        ADD 
          CONSTRAINT FK_AD1CA87234ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_package_preview_token DROP CONSTRAINT fk_ad1ca87234ecb4e6');
        $this->addSql('ALTER TABLE 
          swp_package_preview_token 
        ADD 
          CONSTRAINT fk_ad1ca87234ecb4e6 FOREIGN KEY (route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
