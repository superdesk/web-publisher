<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\Package;
use SWP\Bundle\CoreBundle\Model\PackageInterface;

final class RouteRemoveListener
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * RouteRemoveListener constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ArticleEvent $event
     */
    public function onDelete(RouteEvent $event)
    {
        $route = $event->getRoute();
        $queryBuilder = $this->manager->createQueryBuilder();
        $queryBuilder->update(Package::class, 'p')
            ->set('p.status', $queryBuilder->expr()->literal(PackageInterface::STATUS_USABLE))
            ->where('p.id IN (SELECT a.id FROM SWP\Bundle\CoreBundle\Model\Article a WHERE a.route = :route AND a.package = p.id)')
            ->setParameter('route', $route->getId())
            ->getQuery()
            ->execute();

        $queryBuilder = $this->manager->createQueryBuilder();
        $queryBuilder->update(Article::class, 'a')
            ->set('a.status', $queryBuilder->expr()->literal(ArticleInterface::STATUS_NEW))
            ->where('a.route = :route')
            ->setParameter('route', $route->getId())
            ->getQuery()
            ->execute();
    }
}
