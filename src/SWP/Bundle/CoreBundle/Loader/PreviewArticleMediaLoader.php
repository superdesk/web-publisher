<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Loader;

use SWP\Bundle\ContentBundle\Loader\PaginatedLoader;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

class PreviewArticleMediaLoader extends PaginatedLoader implements LoaderInterface
{
    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * ArticleMediaLoader constructor.
     *
     * @param MetaFactory $metaFactory
     * @param Context     $context
     */
    public function __construct(MetaFactory $metaFactory, Context $context)
    {
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function load($metaType, $withParameters = [], $withoutParameters = [], $responseType = self::SINGLE)
    {
        if (LoaderInterface::COLLECTION === $responseType) {
            $criteria = new Criteria();
            if (array_key_exists('article', $withParameters) && $withParameters['article'] instanceof Meta) {
                $article = $withParameters['article']->getValues();
            } elseif (isset($this->context->article) && null !== $this->context->article) {
                $article = $this->context->article->getValues();
            } else {
                return false;
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $withParameters);
            $articleMedia = $article->getMedia();

            if (0 < \count($articleMedia)) {
                $collectionCriteria = new \Doctrine\Common\Collections\Criteria(
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
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(string $type): bool
    {
        return 'articleMedia' === $type && $this->context->isPreviewMode();
    }
}
