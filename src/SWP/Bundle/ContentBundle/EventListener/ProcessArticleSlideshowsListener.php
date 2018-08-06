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
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessorInterface;
use SWP\Component\Bridge\Model\ItemInterface;
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

        if (null === $package || (null !== $package && 0 === \count($package->getGroups()))) {
            return;
        }

        $this->removeOldArticleMedia($article);

        foreach ($package->getGroups() as $packageGroup) {
            $slideshow = $this->slideshowFactory->create();
            $slideshow->setCode($packageGroup->getCode());

            foreach ($packageGroup->getItems() as $item) {
                if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_FILE === $item->getType()) {
                    $this->removeArticleMediaIfNeeded($item->getName(), $article);

                    $articleMedia = $this->handleMedia($article, $item->getName(), $item);
                    $this->articleMediaRepository->persist($articleMedia);

                    $slideshow->addItem($articleMedia);
                }
            }

            $article->addSlideshow($slideshow);
        }
    }
}
