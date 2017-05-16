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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ArticleLoader.
 */
class ArticleLoader extends PaginatedLoader implements LoaderInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * @var RouteProviderInterface
     */
    protected $routeProvider;

    /**
     * @var ObjectManager
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
     * @param ArticleRepositoryInterface $articleRepository
     * @param RouteProviderInterface     $routeProvider
     * @param ObjectManager              $dm
     * @param MetaFactoryInterface       $metaFactory
     * @param Context                    $context
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        RouteProviderInterface $routeProvider,
        ObjectManager $dm,
        MetaFactoryInterface $metaFactory,
        Context $context
    ) {
        $this->articleRepository = $articleRepository;
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
        $criteria = new Criteria();
        if ($type === 'article' && $responseType === LoaderInterface::SINGLE) {
            $article = null;
            if (array_key_exists('article', $parameters) && $parameters['article'] instanceof ArticleInterface) {
                $this->dm->detach($parameters['article']);
                $article = $this->articleRepository->findOneBy(['id' => $parameters['article']->getId()]);
                unset($parameters['article']);
            } elseif (array_key_exists('slug', $parameters)) {
                $article = $this->articleRepository->findOneBySlug($parameters['slug']);
            }

            try {
                return $this->getArticleMeta($article);
            } catch (NotFoundHttpException $e) {
                return;
            }
        } elseif ($type === 'articles' && $responseType === LoaderInterface::COLLECTION) {
            $currentPage = $this->context['route'];
            $route = null;

            if ($currentPage) {
                $route = $currentPage->getValues();
            }

            if (array_key_exists('route', $parameters)) {
                if (null === $route || ($route instanceof RouteInterface && $route->getId() !== $parameters['route'])) {
                    if (is_int($parameters['route'])) {
                        $route = $this->routeProvider->getOneById($parameters['route']);
                    } elseif (is_string($parameters['route'])) {
                        $route = $this->routeProvider->getOneByStaticPrefix($parameters['route']);
                    }

                    if (null === $route) {
                        // if Route parameter was passed but it was not found - don't return articles not filtered by route
                        return;
                    }
                }
            }

            if (null !== $route) {
                if ($route instanceof RouteInterface && RouteInterface::TYPE_COLLECTION === $route->getType()) {
                    $criteria->set('route', $route);
                }
            }

            if (isset($parameters['metadata'])) {
                $criteria->set('metadata', $parameters['metadata']);
            }

            if (isset($parameters['keywords'])) {
                $criteria->set('keywords', $parameters['keywords']);
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $parameters);
            $countCriteria = clone $criteria;
            $articles = $this->articleRepository->findArticlesByCriteria($criteria, $criteria->get('order', []));
            $articlesCollection = new ArrayCollection($articles);
            if ($articlesCollection->count() > 0) {
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($this->articleRepository->countByCriteria($countCriteria));
                foreach ($articlesCollection as $article) {
                    $articleMeta = $this->getArticleMeta($article);
                    if (null !== $articleMeta) {
                        $metaCollection->add($articleMeta);
                    }
                }
                unset($articlesCollection, $route, $criteria);

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
    public function isSupported(string $type): bool
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
}
