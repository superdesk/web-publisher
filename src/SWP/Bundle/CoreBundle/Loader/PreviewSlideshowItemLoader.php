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

namespace SWP\Bundle\CoreBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Loader\PaginatedLoader;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class PreviewSlideshowItemLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPE = 'slideshowItems';

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        MetaFactoryInterface $metaFactory,
        Context $context
    ) {
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    public function load($type, $withParameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();

        if (array_key_exists('article', $withParameters) && $withParameters['article'] instanceof Meta) {
            $article = $withParameters['article']->getValues();
        } elseif (isset($this->context->article)) {
            $article = $this->context->article->getValues();
        } else {
            return false;
        }

        if (LoaderInterface::COLLECTION === $responseType) {
            $slideshow = null;

            if (array_key_exists('slideshow', $withParameters)
                && ($slideshowParam = $withParameters['slideshow']) instanceof Meta
                && $slideshowParam->getValues() instanceof SlideshowInterface) {
                $slideshow = $slideshowParam->getValues();
            }

            if (null === $slideshow) {
                $slideshowItems = new ArrayCollection();
                foreach ($article->getSlideshows() as $slideshow) {
                    foreach ($slideshow->getSlideshowItems() as $slideshowItem) {
                        $slideshowItems->add($slideshowItem);
                    }
                }
            } else {
                $slideshowItems = $slideshow->getSlideshowItems();
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $withParameters);

            if (0 < \count($slideshowItems)) {
                $collectionCriteria = new \Doctrine\Common\Collections\Criteria(
                    null,
                    $criteria->get('order'),
                    $criteria->get('firstResult'),
                    $criteria->get('maxResults')
                );

                $count = $slideshowItems->count();
                $articleMedia = $slideshowItems->matching($collectionCriteria);
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($count);

                foreach ($articleMedia as $media) {
                    $metaCollection->add($this->metaFactory->create($media));
                }

                return $metaCollection;
            }
        }
    }

    public function isSupported(string $type): bool
    {
        return self::SUPPORTED_TYPE === $type && $this->context->isPreviewMode();
    }
}
