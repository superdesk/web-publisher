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

use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Loader\PaginatedLoader;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class PreviewArticleMediaLoader extends PaginatedLoader implements LoaderInterface
{
    /**
     * @var MediaFactoryInterface
     */
    protected $mediaFactory;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * ArticleMediaLoader constructor.
     *
     * @param MediaFactoryInterface $mediaFactory
     * @param MetaFactory           $metaFactory
     * @param Context               $context
     * @param RequestStack          $requestStack
     */
    public function __construct(MediaFactoryInterface $mediaFactory, MetaFactory $metaFactory, Context $context, RequestStack $requestStack)
    {
        $this->mediaFactory = $mediaFactory;
        $this->metaFactory = $metaFactory;
        $this->context = $context;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function load($metaType, $parameters = [], $responseType = self::SINGLE)
    {
        if (LoaderInterface::COLLECTION === $responseType) {
            $criteria = new Criteria();
            if (array_key_exists('article', $parameters) && $parameters['article'] instanceof Meta) {
                $article = $parameters['article']->getValues();
            } elseif (isset($this->context->article) && null !== $this->context->article) {
                $article = $this->context->article->getValues();
            } else {
                return false;
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $parameters);
            $articleMedia = $article->getMedia();
            if (0 < count($articleMedia)) {
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
        $isPreview = $this->requestStack->getMasterRequest()->attributes->has(LoaderInterface::PREVIEW_MODE);

        return in_array($type, ['articleMedia']) && $isPreview;
    }

    /**
     * @param ArticleInterface $article
     * @param string           $key
     * @param ItemInterface    $item
     *
     * @return ArticleMediaInterface
     */
    private function handleMedia(ArticleInterface $article, string $key, ItemInterface $item)
    {
        $articleMedia = $this->mediaFactory->create($article, $key, $item);
        if (ArticleInterface::KEY_FEATURE_MEDIA === $key) {
            $article->setFeatureMedia($articleMedia);
        }

        return $articleMedia;
    }
}
