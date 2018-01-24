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

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\ORM\ArticleProvider;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ArticleLoader.
 */
class ArticleLoader extends PaginatedLoader implements LoaderInterface
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
        ArticleProvider $articleProvider,
        RouteProviderInterface $routeProvider,
        ObjectManager $dm,
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
     *  {@inheritdoc}
     */
    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();
        if ('article' === $type && LoaderInterface::SINGLE === $responseType) {
            $article = null;
            if (array_key_exists('article', $parameters) && $parameters['article'] instanceof ArticleInterface) {
                $this->dm->detach($parameters['article']);
                $criteria->set('id', $parameters['article']->getId());
                unset($parameters['article']);
            } elseif (array_key_exists('slug', $parameters)) {
                $criteria->set('slug', $parameters['slug']);
            }

            try {
                $article = $this->articleProvider->getOneByCriteria($criteria);
            } catch (NotFoundHttpException $e) {
                return;
            }

            try {
                return $this->getArticleMeta($article);
            } catch (NotFoundHttpException $e) {
                return;
            }
        } elseif ('articles' === $type && LoaderInterface::COLLECTION === $responseType) {
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
                    } elseif (is_array($parameters['route'])) {
                        $route = $parameters['route'];
                    }

                    if (null === $route) {
                        // if Route parameter was passed but it was not found - don't return articles not filtered by route
                        return;
                    }
                }
            }

            if (null !== $route && ($route instanceof RouteInterface && RouteInterface::TYPE_COLLECTION === $route->getType() || is_array($route))) {
                $criteria->set('route', $route);
            }

            foreach (['metadata', 'keywords', 'source', 'author'] as $item) {
                if (isset($parameters[$item])) {
                    $criteria->set($item, $parameters[$item]);
                }

                if (isset($withoutParameters[$item])) {
                    $criteria->set('exclude_'.$item, $withoutParameters[$item]);
                }
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $parameters);
            $countCriteria = clone $criteria;
            $articlesCollection = $this->articleProvider->getManyByCriteria($criteria, $criteria->get('order', []));

            if ($articlesCollection->count() > 0) {
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($this->articleProvider->getCountByCriteria($countCriteria));
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

    /**
     * @param $article
     *
     * @return \SWP\Component\TemplatesSystem\Gimme\Meta\Meta|void
     */
    private function getArticleMeta($article)
    {
        if (null !== $article) {
            return $this->metaFactory->create($article);
        }

        return;
    }
}
