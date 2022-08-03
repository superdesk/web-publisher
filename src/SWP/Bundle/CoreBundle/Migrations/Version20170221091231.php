<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170221091231 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_container DROP CONSTRAINT FK_CF0E49301DFA7C8F');
        $this->addSql('ALTER TABLE swp_revision_log DROP CONSTRAINT FK_A1F96AFD9AC03385');
        $this->addSql('ALTER TABLE swp_revision_log DROP CONSTRAINT FK_A1F96AFD21852C2F');
        $this->addSql('TRUNCATE TABLE swp_revision');
        $this->addSql('ALTER TABLE swp_container ADD CONSTRAINT FK_CF0E49301DFA7C8F FOREIGN KEY (revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_revision_log ADD CONSTRAINT FK_A1F96AFD9AC03385 FOREIGN KEY (target_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_revision_log ADD CONSTRAINT FK_A1F96AFD21852C2F FOREIGN KEY (source_revision_id) REFERENCES swp_revision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
