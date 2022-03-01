<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Migrations\IrreversibleMigrationException;
use Doctrine\DBAL\Schema\Schema;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Add article statistics to articles.
 */
class Version20180118194100 extends AbstractMigration implements ContainerAwareInterface
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

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $this->container->get('event_dispatcher')->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

        $articles = $entityManager
            ->createQuery('SELECT partial a.{id,tenantCode},  partial es.{id} FROM SWP\Bundle\CoreBundle\Model\Article a LEFT JOIN a.articleStatistics es')
            ->getArrayResult();

        if (empty($articles)) {
            return;
        }

        $dbConnection = $entityManager->getConnection();
        $nextvalQuery = $dbConnection->getDatabasePlatform()->getSequenceNextValSQL('swp_article_statistics_id_seq');
        $newId = (int) $dbConnection->fetchColumn($nextvalQuery);

        /* @var ArticleInterface $article */
        foreach ($articles as $article) {
            if (null !== $article['articleStatistics']) {
                continue;
            }

            $this->addSql('INSERT INTO swp_article_statistics (id, article_id, tenant_code, created_at) VALUES (:id, :article, :tenantCode, :createdAt)', [
                'id' => $newId,
                'article' => $article['id'],
                'tenantCode' => $article['tenantCode'],
                'createdAt' => (new \DateTime('now'))->format('Y-m-d h:i:sT'),
            ]);

            ++$newId;
        }
    }

    /**
     * @param Schema $schema
     *
     * @throws IrreversibleMigrationException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        // No way to rollback this
        throw new IrreversibleMigrationException();
    }
}
