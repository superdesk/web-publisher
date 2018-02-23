<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180223095718 extends AbstractMigration implements ContainerAwareInterface
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

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT r FROM SWP\Bundle\CoreBundle\Model\Route r WHERE r.slug IS NULL');
        $routes = $query->getResult();

        foreach ($routes as $route) {
            $qb = $entityManager->createQueryBuilder();
            $query = $qb->update(RouteInterface::class, 'r')
                ->set('r.slug', '?1')
                ->where('r.id = ?2')
                ->setParameter(1, Transliterator::transliterate($route->getName()))
                ->setParameter(2, $route->getId())
                ->getQuery();

            $query->execute();
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT r FROM SWP\Bundle\CoreBundle\Model\Route r');
        $routes = $query->getResult();

        foreach ($routes as $route) {
            if ($route->getSlug() === Transliterator::transliterate($route->getName())) {
                $qb = $entityManager->createQueryBuilder();
                $query = $qb->update(RouteInterface::class, 'r')
                    ->set('r.slug', '?1')
                    ->where('r.id = ?2')
                    ->setParameter(1, null)
                    ->setParameter(2, $route->getId())
                    ->getQuery();

                $query->execute();
            }
        }
    }
}
