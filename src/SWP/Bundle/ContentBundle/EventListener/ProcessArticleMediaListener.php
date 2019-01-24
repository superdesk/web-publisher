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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;

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
        $this->removeIfNeededAndPersist($package, $article);
    }

    private function removeIfNeededAndPersist(PackageInterface $package, ArticleInterface $article): void
    {
        $items = $this->getItemsWithoutSlideshows($package);

        foreach ($items as $packageItem) {
            $key = $packageItem->getName();
            if ($this->isTypeAllowed($packageItem->getType())) {
                $this->removeArticleMediaIfNeeded($key, $article);

                $articleMedia = $this->handleMedia($article, $key, $packageItem);
                $this->articleMediaRepository->persist($articleMedia);
            }

            $this->removeIfNeededAndPersist($packageItem, $article);
        }
    }

    private function getItemsWithoutSlideshows(PackageInterface $package): Collection
    {
        $guids = $this->getSlideshowsGuidsToFilterOut($package);

        if (null !== $package->getItems() && 0 !== $package->getItems()->count()) {
            return $package->getItems()->filter(
                function ($entry) use ($guids) {
                    return !\in_array($entry->getGuid(), $guids, true);
                }
            );
        }

        return new ArrayCollection();
    }

    private function getSlideshowsGuidsToFilterOut(PackageInterface $package): array
    {
        $guids = [];
        foreach ($package->getGroups() as $packageGroup) {
            foreach ($packageGroup->getItems() as $item) {
                if ($this->isTypeAllowed($item->getType())) {
                    $guids[] = $item->getGuid();
                }
            }
        }

        return $guids;
    }
}
