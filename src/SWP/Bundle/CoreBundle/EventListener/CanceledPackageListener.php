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
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CanceledPackageListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var ObjectManager
     */
    private $articleManager;

    /**
     * CanceledPackageListener constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ArticleRepositoryInterface $articleRepository,
        ObjectManager $articleManager
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->articleRepository = $articleRepository;
        $this->articleManager = $articleManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function onCanceled(GenericEvent $event)
    {
        $package = $this->getPackage($event);

        if (ContentInterface::STATUS_CANCELED !== $package->getPubStatus()) {
            return;
        }

        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        foreach ($this->articleRepository->findBy(['package' => $package]) as $article) {
            $this->eventDispatcher->dispatch(ArticleEvents::CANCELED, new ArticleEvent($article));
        }

        $this->articleManager->flush();
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
    }

    /**
     * @param GenericEvent $event
     *
     * @return PackageInterface
     */
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
