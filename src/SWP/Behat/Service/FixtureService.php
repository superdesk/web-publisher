<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use BehatExtension\DoctrineDataFixturesExtension\Service\FixtureService as BaseFixtureService;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

final class FixtureService extends BaseFixtureService
{
    private $em;

    public function __construct(ContainerInterface $container, Kernel $kernel)
    {
        parent::__construct($container, $kernel);

        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function reloadFixtures($disableFixtures = false)
    {
        if ($disableFixtures) {
            // Drop Database
            $schemaTool = new SchemaTool($this->em);
            $schemaTool->dropDatabase();

            //Create Database
            $metadata = $this->em->getMetadataFactory()->getAllMetadata();
            $schemaTool = new SchemaTool($this->em);
            $schemaTool->createSchema($metadata);
        } else {
            parent::reloadFixtures();
        }
    }
}
