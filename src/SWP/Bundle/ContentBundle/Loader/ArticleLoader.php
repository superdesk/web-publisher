<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Loader;

use Jackalope\Query\SqlQuery;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Doctrine\ODM\PHPCR\DocumentManager;

/**
 * Class ArticleLoader.
 */
class ArticleLoader implements LoaderInterface
{
    /**
     * @var ArticleProviderInterface
     */
    protected $articleProvider;

    /**
     * @var RouteProviderInterface
     */
    protected $routeProvider;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var string
     */
    protected $routeBasepaths;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * ArticleLoader constructor.
     *
     * @param ArticleProviderInterface $articleProvider
     * @param RouteProviderInterface   $routeProvider
     * @param DocumentManager          $dm
     * @param MetaFactoryInterface     $metaFactory
     * @param Context                  $context
     */
    public function __construct(
        ArticleProviderInterface $articleProvider,
        RouteProviderInterface $routeProvider,
        DocumentManager $dm,
        MetaFactoryInterface $metaFactory,
        Context $context
    ) {
        $this->articleProvider = $articleProvider;
        $this->routeProvider = $routeProvider;
        $this->dm = $dm;
        $this->metaFactory = $metaFactory;
        $this->context = $context;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Loader loads articles from Content Repository",
     *     parameters={
     *         contentPath="SINGLE|required content path",
     *         slug="SINGLE|required content slug",
     *         pageName="COLLECTiON|name of Page for required articles"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return Meta|Meta[]|bool false if meta cannot be loaded, a Meta instance otherwise
     *
     * @throws \Exception
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $article = null;
        $criteria = null;

        if ($responseType === LoaderInterface::SINGLE) {
            //$expressionBuilder = new ExpressionBuilder();
            if (array_key_exists('contentPath', $parameters)) {
                //$criteria->where($expressionBuilder->eq('id', $parameters['contentPath']));
            } elseif (array_key_exists('article', $parameters)) {
                $this->dm->detach($parameters['article']);
                //$criteria->where($expressionBuilder->eq('id', $parameters['article']->getId()));
            } elseif (array_key_exists('slug', $parameters)) {
                //                $criteria->where($expressionBuilder->andX(
//                    $expressionBuilder->eq('slug', $parameters['slug']),
//                    $expressionBuilder->eq('status', ArticleInterface::STATUS_PUBLISHED)
//                ));
            }

            //$criteria = new Criteria($expressionBuilder);
            //dump($criteria);
            $article = $this->articleProvider->getOneByCriteria($criteria);

            return $this->getArticleMeta($article);
        } elseif ($responseType === LoaderInterface::COLLECTION) {
            $route = null;
            $currentPage = $this->context->getCurrentPage();
            if (array_key_exists('route', $parameters)) {
                if (null !== $currentPage && $currentPage->getValues()->getId() === $parameters['route']) {
                    $route = $currentPage->getValues();
                } else {
                    $route = $this->routeProvider->getOneById($parameters['route']);
                }
            } elseif (null !== $currentPage) {
                $route = $currentPage->getValues();
            }

            if (null !== $route && is_object($route)) {
                $metaCollection = new MetaCollection();
                //$expressionBuilder = new ExpressionBuilder();
                //$criteria = new Criteria($expressionBuilder->eq('route', $route));
                //dump($criteria->getWhereExpression());
                //die;
//                $routeIdentifier = $this->dm->getNodeForDocument($route)->getIdentifier();
//                $metaCollection->setTotalItemsCount($this->getRouteArticlesQuery($routeIdentifier, [])->execute()->getRows()->count());
//                $query = $this->getRouteArticlesQuery($routeIdentifier, $parameters);
//                $articles = $this->dm->getDocumentsByPhpcrQuery($query, Article::class);

//                if (isset($parameters['limit'])) {
//                    $query->setLimit($parameters['limit']);
//                }

//                if (isset($parameters['start'])) {
//                    $query->setOffset($parameters['start']);
//                }

//                foreach ($articles as $article) {
//                    $articleMeta = $this->getArticleMeta($article);
//                    if (null !== $articleMeta) {
//                        $metaCollection->add($articleMeta);
//                    }
//                }

                return $metaCollection;
            }
        }

        return;
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported(string $type) : bool
    {
        return in_array($type, ['articles', 'article']);
    }

    private function getArticleMeta($article)
    {
        if (null !== $article) {
            return $this->metaFactory->create($article);
        }

        return;
    }

    /**
     * @param string $routeIdentifier
     * @param array  $parameters
     *
     * @return SqlQuery
     */
    private function getRouteArticlesQuery(string $routeIdentifier, array $parameters) : SqlQuery
    {
        $order = [];
        if (array_key_exists('order', $parameters) && is_array($parameters['order'])) {
            $order = $parameters['order'];
        }

        return $this->articleProvider->getRouteArticlesQuery($routeIdentifier, $order);
    }
}
