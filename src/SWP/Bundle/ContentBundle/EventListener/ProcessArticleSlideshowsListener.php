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

use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\SlideshowItem;
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessorInterface;
use SWP\Component\Bridge\Model\GroupInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ProcessArticleSlideshowsListener extends AbstractArticleMediaListener
{
    /**
     * @var FactoryInterface
     */
    private $slideshowFactory;

    public function __construct(
        ArticleMediaRepositoryInterface $articleMediaRepository,
        MediaFactoryInterface $mediaFactory,
        ArticleBodyProcessorInterface $articleBodyProcessor,
        FactoryInterface $slideshowFactory
    ) {
        $this->slideshowFactory = $slideshowFactory;

        parent::__construct($articleMediaRepository, $mediaFactory, $articleBodyProcessor);
    }

    public function onArticleCreate(ArticleEvent $event): void
    {
        $package = $event->getPackage();
        $article = $event->getArticle();

        $groups = $package->getGroups()->filter(function ($group) {
            return GroupInterface::TYPE_RELATED !== $group->getType();
        });

        if (null === $package || (null !== $package && 0 === \count($groups))) {
            return;
        }

        foreach ($package->getGroups() as $packageGroup) {
            foreach ($packageGroup->getItems() as $item) {
                if ($this->isTypeAllowed($item->getType())) {
                    $this->removeArticleMediaIfNeeded($item->getName(), $article);
                }
            }
        }

        $this->removeOldArticleSlideshows($article);

        foreach ($groups as $packageGroup) {
            $slideshow = $this->slideshowFactory->create();
            $slideshow->setCode($packageGroup->getCode());
            $slideshow->setArticle($article);

            foreach ($packageGroup->getItems() as $item) {
                if ($this->isTypeAllowed($item->getType())) {
                    $slideshowItem = new SlideshowItem();
                    $articleMedia = $this->handleMedia($article, $item->getName(), $item);

                    $this->articleMediaRepository->persist($articleMedia);

                    $slideshowItem->setArticleMedia($articleMedia);
                    $slideshowItem->setSlideshow($slideshow);

                    $this->articleMediaRepository->persist($slideshowItem);
                }
            }
        }
    }

    public function removeOldArticleSlideshows(ArticleInterface $article): void
    {
        foreach ($article->getSlideshows() as $slideshow) {
            $article->removeSlideshow($slideshow);
        }
    }
}
