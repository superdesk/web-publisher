<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\Bridge\Model\PackageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleMetadataSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::PRE_CREATE => 'populateMetadata',
        ];
    }

    /**
     * @param ArticleEvent $event
     */
    public function populateMetadata(ArticleEvent $event)
    {
        $package = $this->getPackage($event);
        $article = $event->getArticle();

        $article->setMetadata([
            'subject' => $package->getSubjects(),
            'urgency' => $package->getUrgency(),
            'priority' => $package->getPriority(),
            'located' => $package->getLocated(),
            'place' => $package->getPlaces(),
            'service' => $package->getServices(),
            'type' => $package->getType(),
            'byline' => $package->getByLine(),
            'guid' => $package->getGuid(),
            'edNote' => $package->getEdNote(),
            'genre' => $package->getGenre(),
            'language' => $package->getLanguage(),
            'versioncreated' => $package->getCreatedAt(),
        ]);
    }

    private function getPackage(ArticleEvent $event)
    {
        $package = $event->getPackage();

        if (!$package instanceof PackageInterface) {
            throw new \InvalidArgumentException(
                sprintf('Expected argument of type "%s", "%s" given.', PackageInterface::class, $package)
            );
        }

        return $package;
    }
}
