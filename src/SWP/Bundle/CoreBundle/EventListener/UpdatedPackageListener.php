<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Hydrator\ArticleHydratorInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UpdatedPackageListener
{
    private $articleHydrator;

    private $articleManager;

    private $articleRepository;

    private $eventDispatcher;

    private $elasticaObjectPersister;

    public function __construct(
        ArticleHydratorInterface $articleHydrator,
        ObjectManager $articleManager,
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectPersisterInterface $elasticaObjectPersister
    ) {
        $this->articleHydrator = $articleHydrator;
        $this->articleManager = $articleManager;
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->elasticaObjectPersister = $elasticaObjectPersister;
    }

    public function onUpdated(GenericEvent $event): void
    {
        $package = $this->getPackage($event);
        $this->elasticaObjectPersister->replaceOne($package);

        if (ContentInterface::STATUS_USABLE === $package->getPubStatus()) {
            $this->handleArticlesUpdate($package);
        }

        if (in_array($package->getPubStatus(), [ContentInterface::STATUS_CANCELED, ContentInterface::STATUS_UNPUBLISHED], true)) {
            $this->handleCancelationAndUnpublishing($package);

            return;
        }
    }

    private function handleArticlesUpdate(PackageInterface $package)
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        foreach ($this->articleRepository->getArticlesByPackage($package)->getQuery()->getResult() as $article) {
            $article = $this->articleHydrator->hydrate($article, $package);
            $this->eventDispatcher->dispatch(ArticleEvents::PRE_UPDATE, new ArticleEvent($article, $package, ArticleEvents::PRE_UPDATE));
            // Flush in loop to emit POST_UPDATE article event
            $this->articleManager->flush();
            $this->eventDispatcher->dispatch(ArticleEvents::POST_UPDATE, new ArticleEvent($article, $package, ArticleEvents::POST_UPDATE));
        }

        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
    }

    private function handleCancelationAndUnpublishing(PackageInterface $package)
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        foreach ($this->articleRepository->findBy(['package' => $package]) as $article) {
            if (ContentInterface::STATUS_CANCELED === $package->getPubStatus()) {
                $this->eventDispatcher->dispatch(
                    ArticleEvents::CANCELED,
                    new ArticleEvent($article, null, ArticleEvents::CANCELED)
                );
            } elseif (ContentInterface::STATUS_UNPUBLISHED === $package->getPubStatus()) {
                $this->eventDispatcher->dispatch(
                    ArticleEvents::UNPUBLISH,
                    new ArticleEvent($article, null, ArticleEvents::UNPUBLISH)
                );
            }
        }

        $this->articleManager->flush();
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
    }

    private function getPackage(GenericEvent $event)
    {
        /** @var PackageInterface $package */
        if (!($package = $event->getSubject()) instanceof PackageInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($package) ? get_class($package) : gettype($package),
                PackageInterface::class
            );
        }

        return $package;
    }
}
