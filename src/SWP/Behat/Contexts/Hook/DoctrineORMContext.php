<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineORMContext implements Context
{
    public static $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        self::$entityManager = $entityManager;
    }

    /**
     * @BeforeFeature
     */
    public static function purgeDatabase(BeforeFeatureScope $scope): void
    {
        if (\in_array('disable-fixtures', $scope->getFeature()->getTags(), true)) {
            self::$entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
            $purger = new ORMPurger(self::$entityManager);
            $purger->purge();
            self::$entityManager->clear();
        }
    }
}
