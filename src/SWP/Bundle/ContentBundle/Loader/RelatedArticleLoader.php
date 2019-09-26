<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Loader;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\RelatedArticleRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class RelatedArticleLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPE = 'relatedArticles';

    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * @var RelatedArticleRepositoryInterface
     */
    protected $relatedArticleRepository;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        RelatedArticleRepositoryInterface $relatedArticleRepository,
        MetaFactoryInterface $metaFactory,
        Context $context
    ) {
        $this->articleRepository = $articleRepository;
        $this->relatedArticleRepository = $relatedArticleRepository;
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        if (LoaderInterface::COLLECTION === $responseType) {
            $criteria = new Criteria();
            if (array_key_exists('article', $parameters) && $parameters['article'] instanceof Meta) {
                $criteria->set('article', $parameters['article']->getValues());
            } elseif (isset($this->context->article)) {
                $criteria->set('article', $this->context->article->getValues());
            } else {
                return false;
            }

            $this->applyPaginationToCriteria($criteria, $parameters);
            $relatedArticles = $this->relatedArticleRepository->getByCriteria($criteria, $criteria->get('order', ['id' => 'desc']));
            $relatedArticlesCount = $this->relatedArticleRepository->countByCriteria($criteria);

            if (\count($relatedArticles) > 0) {
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($relatedArticlesCount);

                foreach ($relatedArticles as $relatedArticle) {
                    $meta = $this->metaFactory->create($relatedArticle);
                    if (null !== $meta) {
                        $metaCollection->add($meta);
                    }
                }

                unset($articlesCollection, $route, $criteria);

                return $metaCollection;
            }
        }
    }

    public function isSupported(string $type): bool
    {
        return self::SUPPORTED_TYPE === $type && !$this->context->isPreviewMode();
    }
}
