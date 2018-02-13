<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Migrations\IrreversibleMigrationException;
use Doctrine\DBAL\Schema\Schema;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatistics;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->container->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $query = $this->container->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT count(a) FROM SWP\Bundle\CoreBundle\Model\Article a');
        $articlesCount = $query->getSingleScalarResult();

        if ($articlesCount === 0) {
            return;
        }

        $articles = $this->container->get('swp.repository.article')->findAll();

        /* @var ArticleInterface $article */
        foreach ($articles as $article) {
            if (null !== $article->getArticleStatistics()) {
                continue;
            }

            $articleStatistics = new ArticleStatistics();
            $articleStatistics->setArticle($article);
            $articleStatistics->setTenantCode($article->getTenantCode());
            $this->container->get('doctrine')->getManager()->persist($articleStatistics);
        }
        $this->container->get('doctrine')->getManager()->flush();
    }

    /**
     * @param Schema $schema
     *
     * @throws IrreversibleMigrationException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        // No way to rollback this
        throw new IrreversibleMigrationException();
    }
}
