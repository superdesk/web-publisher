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
        $routeRepository = $this->container->get('swp.repository.route');

        /** @var RouteInterface $route */
        foreach ($routeRepository->findBy(['slug' => null]) as $route) {
            $route->setSlug(Transliterator::transliterate($route->getName()));
        }

        $entityManager->flush();
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $routeRepository = $this->container->get('swp.repository.route');

        /** @var RouteInterface $route */
        foreach ($routeRepository->findAll() as $route) {
            if ($route->getSlug() === Transliterator::transliterate($route->getName())) {
                $route->setSlug(null);
            }
        }

        $entityManager->flush();
    }
}
