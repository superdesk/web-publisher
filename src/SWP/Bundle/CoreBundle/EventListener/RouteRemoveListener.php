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
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\Package;
use SWP\Bundle\CoreBundle\Repository\MenuItemRepositoryInterface;
use SWP\Component\Bridge\Model\ContentInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final class RouteRemoveListener
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var MenuItemRepositoryInterface
     */
    private $menuItemRepository;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * RouteRemoveListener constructor.
     *
     * @param EntityManagerInterface      $manager
     * @param MenuItemRepositoryInterface $menuItemRepository
     */
    public function __construct(EntityManagerInterface $manager, MenuItemRepositoryInterface $menuItemRepository, ProducerInterface $producer)
    {
        $this->manager = $manager;
        $this->menuItemRepository = $menuItemRepository;
        $this->producer = $producer;
    }

    /**
     * @param ArticleEvent $event
     */
    public function onDelete(RouteEvent $event)
    {
        $route = $event->getRoute();
        if (\count($this->menuItemRepository->findByRoute($route->getId()))) {
            throw new ConflictHttpException('Route has menu attached to it.');
        }

        $queryBuilder = $this->manager->createQueryBuilder();
        $queryBuilder->update(Package::class, 'p')
            ->set('p.status', $queryBuilder->expr()->literal(ContentInterface::STATUS_USABLE))
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

        $this->producer->publish(serialize(new ArrayInput([
            'command' => 'fos:elastica:reset',
            '--index' => 'swp',
            '--type' => 'article',
        ])));
    }
}
