<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use function in_array;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\MediaAwareInterface;

class ProcessArticleMediaListener extends AbstractArticleMediaListener
{
    public function onArticleCreate(ArticleEvent $event): void
    {
        $package = $event->getPackage();
        $article = $event->getArticle();

        if (null === $package || (null !== $package && 0 === \count($package->getItems()))) {
            return;
        }

        $this->removeOldArticleMedia($article);

        $guids = [];
        foreach ($package->getGroups() as $packageGroup) {
            foreach ($packageGroup->getItems() as $item) {
                if ($this->isTypeAllowed($item->getType())) {
                    $guids[] = $item->getGuid();
                }
            }
        }

        $items = $package->getItems()->filter(
            static function ($entry) use ($guids) {
                return !in_array($entry->getGuid(), $guids, true);
            }
        );

        foreach ($items as $packageItem) {
            $key = $packageItem->getName();
            if ($this->isTypeAllowed($packageItem->getType())) {
                $articleMedia = $this->handleMedia($article, $key, $packageItem);
                $this->articleMediaRepository->persist($articleMedia);

                if (MediaAwareInterface::KEY_FEATURE_MEDIA === $key) {
                    $article->setFeatureMedia($articleMedia);
                }
            }
        }
    }
}
