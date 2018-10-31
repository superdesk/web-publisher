<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Doctrine\SlideshowItemRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class SlideshowItemLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPE = 'slideshowItems';

    /**
     * @var SlideshowItemRepositoryInterface
     */
    protected $slideshowItemRepository;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    protected $slideshowRepository;

    public function __construct(
        SlideshowItemRepositoryInterface $slideshowItemRepository,
        SlideshowRepositoryInterface $slideshowRepository,
        MetaFactoryInterface $metaFactory,
        Context $context
    ) {
        $this->slideshowItemRepository = $slideshowItemRepository;
        $this->slideshowRepository = $slideshowRepository;
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();

        if (LoaderInterface::COLLECTION === $responseType) {
            if (array_key_exists('article', $parameters) && $parameters['article'] instanceof Meta) {
                $criteria->set('article', $parameters['article']->getValues());
            } elseif (isset($this->context->article)) {
                $criteria->set('article', $this->context->article->getValues());
            } else {
                return false;
            }

            if (array_key_exists('slideshow', $parameters)
                && $parameters['slideshow'] instanceof Meta
                && $parameters['slideshow']->getValues() instanceof SlideshowInterface) {
                $criteria->set('slideshow', $parameters['slideshow']->getValues());
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $parameters);
            $countCriteria = clone $criteria;
            $slideshowItems = $this->slideshowItemRepository->getByCriteria($criteria, $criteria->get('order', []));
            $itemsCollection = new ArrayCollection($slideshowItems);

            if ($itemsCollection->count() > 0) {
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($this->slideshowItemRepository->countByCriteria($countCriteria));
                foreach ($itemsCollection as $item) {
                    $meta = $this->metaFactory->create($item);

                    if (null !== $meta) {
                        $metaCollection->add($meta);
                    }
                }
                unset($itemsCollection, $criteria);

                return $metaCollection;
            }
        }
    }

    public function isSupported(string $type): bool
    {
        return self::SUPPORTED_TYPE === $type && !$this->context->isPreviewMode();
    }
}
