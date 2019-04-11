<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use BehatExtension\DoctrineDataFixturesExtension\Service\FixtureService as BaseFixtureService;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

final class FixtureService extends BaseFixtureService
{
    private $doctrine;

    public function __construct(ContainerInterface $container, Kernel $kernel)
    {
        parent::__construct($container, $kernel);

        $this->doctrine = $kernel->getContainer()->get('doctrine');
    }

    public function reloadFixtures($disableFixtures = false)
    {
        if ($disableFixtures) {
            $purger = new ORMPurger($this->doctrine->getManager());
            $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
            $executor = new ORMExecutor($this->doctrine->getManager(), $purger);
            $executor->setReferenceRepository($this->getReferenceRepository());
            $executor->purge();
        } else {
            parent::reloadFixtures();
        }
    }
}
