<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250403164630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the `swp_failed_queue` table, indexes, notification trigger, and associated function in an idempotent way.';
    }

    public function up(Schema $schema): void
    {
        $tableExists = $this->connection->executeQuery("
            SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE table_schema = 'public'
                AND table_name = 'swp_failed_queue'
            )
        ")->fetchOne();

        if (!$tableExists) {
            $this->addSql('
                CREATE TABLE swp_failed_queue (
                    id BIGSERIAL NOT NULL,
                    body TEXT NOT NULL,
                    headers TEXT NOT NULL,
                    queue_name VARCHAR(190) NOT NULL,
                    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                    available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                    delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                    PRIMARY KEY (id)
                )
            ');

            $this->addSql('CREATE INDEX IDX_21B8E166FB7336F0 ON swp_failed_queue (queue_name)');
            $this->addSql('CREATE INDEX IDX_21B8E166E3BD61CE ON swp_failed_queue (available_at)');
            $this->addSql('CREATE INDEX IDX_21B8E16616BA31DB ON swp_failed_queue (delivered_at)');

            $this->write('Created `swp_failed_queue` table with indexes.');
        } else {
            $this->write('Table `swp_failed_queue` already exists, skipping creation.');
        }

        $functionExists = $this->connection->executeQuery("
            SELECT EXISTS (
                SELECT 1 FROM pg_proc
                JOIN pg_namespace ON pg_proc.pronamespace = pg_namespace.oid
                WHERE proname = 'notify_swp_failed_queue'
                AND pg_namespace.nspname = 'public'
            )
        ")->fetchOne();

        if (!$functionExists) {
            $this->addSql('
                CREATE OR REPLACE FUNCTION notify_swp_failed_queue() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify(\'swp_failed_queue\', NEW.queue_name::text);
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ');

            $this->write('Created `notify_swp_failed_queue` function.');
        } else {
            $this->write('Function `notify_swp_failed_queue` already exists, skipping creation.');
        }

        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON swp_failed_queue');
        $this->addSql('
            CREATE TRIGGER notify_trigger
            AFTER INSERT OR UPDATE ON swp_failed_queue
            FOR EACH ROW
            EXECUTE PROCEDURE notify_swp_failed_queue();
        ');

        $this->write('Recreated `notify_trigger` on `swp_failed_queue`.');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON swp_failed_queue');
        $this->addSql('DROP FUNCTION IF EXISTS notify_swp_failed_queue');
        $this->addSql('DROP TABLE IF EXISTS swp_failed_queue');

        $this->write('Dropped `swp_failed_queue` table, `notify_swp_failed_queue` function, and `notify_trigger` trigger.');
    }
}