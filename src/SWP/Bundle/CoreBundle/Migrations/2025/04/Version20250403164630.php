<?php
declare(strict_types=1);
namespace SWP\Migrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403164630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates swp_failed_queue table with indexes and notification trigger if they don\'t exist';
    }

    public function up(Schema $schema): void
    {
        // Check if table already exists
        $tableExists = $this->connection->executeQuery(
            "SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name = 'swp_failed_queue'
            )"
        )->fetchOne();

        if (!$tableExists) {
            $this->addSql('CREATE TABLE swp_failed_queue (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_21B8E166FB7336F0 ON swp_failed_queue (queue_name)');
            $this->addSql('CREATE INDEX IDX_21B8E166E3BD61CE ON swp_failed_queue (available_at)');
            $this->addSql('CREATE INDEX IDX_21B8E16616BA31DB ON swp_failed_queue (delivered_at)');
            
            $this->write('Created swp_failed_queue table with indexes');
        } else {
            $this->write('Table swp_failed_queue already exists, skipping creation');
            
            // The more reliable approach is to NOT try to create the indexes individually
            // as index names can be ambiguous without schema qualification
        }

        // Check if function exists
        $functionExists = $this->connection->executeQuery(
            "SELECT EXISTS (
                SELECT FROM pg_proc 
                JOIN pg_namespace ON pg_proc.pronamespace = pg_namespace.oid
                WHERE proname = 'notify_swp_failed_queue'
                AND pg_namespace.nspname = 'public'
            )"
        )->fetchOne();

        if (!$functionExists) {
            $this->addSql('CREATE OR REPLACE FUNCTION notify_swp_failed_queue() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify(\'swp_failed_queue\', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;');
            
            $this->write('Created notify_swp_failed_queue function');
        } else {
            $this->write('Function notify_swp_failed_queue already exists, skipping creation');
        }

        // Check if trigger exists before trying to create it
        $triggerExists = $this->connection->executeQuery(
            "SELECT EXISTS (
                SELECT FROM pg_trigger
                JOIN pg_class ON pg_trigger.tgrelid = pg_class.oid
                JOIN pg_namespace ON pg_class.relnamespace = pg_namespace.oid
                WHERE pg_class.relname = 'swp_failed_queue'
                AND pg_trigger.tgname = 'notify_trigger'
                AND pg_namespace.nspname = 'public'
            )"
        )->fetchOne();

        // Always try to drop the trigger if it exists (this is idempotent)
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON swp_failed_queue;');
        
        // Create trigger (safely)
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON swp_failed_queue FOR EACH ROW EXECUTE PROCEDURE notify_swp_failed_queue();');
        
        if (!$triggerExists) {
            $this->write('Created notify_trigger');
        } else {
            $this->write('Recreated notify_trigger');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS swp_failed_queue');
    }
}
