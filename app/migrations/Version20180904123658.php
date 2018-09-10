<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\CoreBundle\Model\ArticleEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180904123658 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_events ADD page_view_source VARCHAR(255) DEFAULT NULL');
    }

    public function postUp(Schema $schema)
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $qb = $entityManager->createQueryBuilder();
        $query = $qb->update(ArticleEvent::class, 'ae')
            ->set('ae.pageViewSource', '?1')
            ->where('ae.action = ?2')
            ->setParameter('1', ArticleEventInterface::PAGEVIEW_SOURCE_INTERNAL)
            ->setParameter('2', ArticleEventInterface::ACTION_PAGEVIEW)
            ->getQuery();

        $query->execute();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article_events DROP page_view_source');
    }
}
