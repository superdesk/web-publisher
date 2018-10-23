<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineORMContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
//        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
//        $purger = new ORMPurger($this->entityManager);
//        $purger->purge();
//        $this->entityManager->clear();
    }
}
