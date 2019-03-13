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

use Doctrine\Common\Collections\Criteria as CollectionCriteria;
use SWP\Bundle\ContentBundle\Loader\PaginatedLoader;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class PreviewSlideshowLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPES = ['slideshows', 'slideshow'];

    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

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

        if (LoaderInterface::SINGLE === $responseType) {
            if (!(array_key_exists('name', $withParameters) && \is_string($withParameters['name']))) {
                return false;
            }

            $criteria = CollectionCriteria::create()->where(CollectionCriteria::expr()->eq('code', $withParameters['name']));
            $slideshow = $article->getSlideshows()->matching($criteria)->first();

            if (null !== $slideshow) {
                return $this->metaFactory->create($slideshow);
            }

            return false;
        }

        $criteria = $this->applyPaginationToCriteria($criteria, $withParameters);
        $articleMedia = $article->getSlideshows();

        if (0 < \count($articleMedia)) {
            $collectionCriteria = new CollectionCriteria(
                null,
                $criteria->get('order'),
                $criteria->get('firstResult'),
                $criteria->get('maxResults')
            );

            $count = $articleMedia->count();

            $articleMedia = $articleMedia->matching($collectionCriteria);

            $metaCollection = new MetaCollection();
            $metaCollection->setTotalItemsCount($count);
            foreach ($articleMedia as $media) {
                $metaCollection->add($this->metaFactory->create($media));
            }

            return $metaCollection;
        }

        return false;
    }

    public function isSupported(string $type): bool
    {
        return \in_array($type, self::SUPPORTED_TYPES, true) && $this->context->isPreviewMode();
    }
}
