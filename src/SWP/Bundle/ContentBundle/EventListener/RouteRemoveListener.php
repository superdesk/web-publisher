<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

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
        $queryBuilder->update(Article::class, 'a')
            ->set('a.status', $queryBuilder->expr()->literal(ArticleInterface::STATUS_NEW))
            ->where('a.route = :route')
            ->setParameter('route', $route->getId())
            ->getQuery()
            ->execute();
    }
}
