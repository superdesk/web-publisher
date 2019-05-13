<?php

declare(strict_types=1);

namespace SWP\Behat\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Query\ResultSetMapping;

class SQLiteForeignKeyEnabler implements EventSubscriber
{
    /** @var EntityManagerInterface */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'preFlush',
        ];
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        if (!$this->manager->getConnection()->getDatabasePlatform() instanceof SqlitePlatform) {
            return;
        }

        $this->manager
            ->createNativeQuery('PRAGMA foreign_keys = ON;', new ResultSetMapping())
            ->execute()
        ;
    }
}
