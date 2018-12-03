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
use SWP\Bundle\ContentBundle\Model\SlideshowItem;
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessorInterface;
use SWP\Component\Bridge\Model\GroupInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ProcessRelatedArticlesListener extends AbstractArticleMediaListener
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

        $relatedItemsGroups = $package->getGroups()->filter(function($group) {
            return $group->getType() === GroupInterface::TYPE_RELATED;
        });

        if (null === $package || (null !== $package && 0 === \count($relatedItemsGroups))) {
            return;
        }

        $this->removeOldArticleMedia($article);


        foreach ($relatedItemsGroups as $relatedItemsGroup) {
            // check in db if item exists by guid
            // if exists, add related item to article
            // else
            // related item does not exist
            // create it
            //
            //
            //

        }
    }
}
