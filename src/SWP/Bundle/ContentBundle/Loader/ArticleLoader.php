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
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\Twig\Cache\CacheBlockTagsCollectorInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleLoader extends PaginatedLoader implements LoaderInterface
{
    protected $articleProvider;

    protected $routeProvider;

    protected $dm;

    protected $metaFactory;

    protected $context;

    private $cacheBlockTagsCollector;

    public function __construct(
        ArticleProviderInterface $articleProvider,
        RouteProviderInterface $routeProvider,
        ObjectManager $dm,
        MetaFactoryInterface $metaFactory,
        Context $context,
        CacheBlockTagsCollectorInterface $cacheBlockTagsCollector
    ) {
        $this->articleProvider = $articleProvider;
        $this->routeProvider = $routeProvider;
        $this->dm = $dm;
        $this->metaFactory = $metaFactory;
        $this->context = $context;
        $this->cacheBlockTagsCollector = $cacheBlockTagsCollector;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();
        if ('article' === $type && LoaderInterface::SINGLE === $responseType) {
            $article = null;
            if (array_key_exists('article', $parameters) && $parameters['article'] instanceof ArticleInterface) {
                try {
                    return $this->getArticleMeta($parameters['article']);
                } catch (NotFoundHttpException $e) {
                    return false;
                }
            } elseif (array_key_exists('slug', $parameters)) {
                if ('' === $parameters['slug']) {
                    $parameters['slug'] = null;
                }
                $criteria->set('slug', $parameters['slug']);
            }

            try {
                $article = $this->articleProvider->getOneByCriteria($criteria);
                $articleBody = $article->getBody();
                $metaArticle = $this->getArticleMeta($article);
                //find local links in the article body
                if (preg_match_all('/<a\s+href=["\']urn:newsml:([^"\']+)["\']/i', $articleBody, $links, PREG_PATTERN_ORDER)) {
                    $all_hrefs = array_unique($links[1]);
                    //replace local links
                    foreach ($all_hrefs as $href) {
                        $code = 'urn:newsml:' . $href;
                        //get the referenced article
                        $refArticle = $this->articleRepository->findOneBy(['code' => $code, 'status' => 'published']);
                        if ($refArticle != null && $refArticle->getRoute()) {
                            $slugName = $refArticle->getSlug();
                            $parameters = ['slug' => $slugName];
                            $realUrl = $this->router->generate($refArticle->getRoute(), $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
                            $articleBody = preg_replace('#urn:newsml:' . $href . '#is', $realUrl, $articleBody);
                        } else {
                            // the article code is not valid - delete the link
                            $re = "/(<a[^href]href=[\"']" . $code . "[^>]*>)([^<>]*|.*)(<\/a>)/m";
                            $subst = "$2";
                            $articleBody = preg_replace($re, $subst, $articleBody);
                        }
                    }
                    $article->setBody($articleBody);
                    $metaArticle->__set("body", $articleBody);
                }
                return $metaArticle;
            } catch (NotFoundHttpException $e) {
                return false;
            }
        } elseif ('articles' === $type && LoaderInterface::COLLECTION === $responseType) {
            $currentPage = $this->context['route'];
            $route = null;

            if ($currentPage) {
                $route = $currentPage->getValues();
            }

            if (array_key_exists('route', $parameters)) {
                if (null === $route || ($route instanceof RouteInterface && $route->getId() !== $parameters['route'])) {
                    $route = $this->routeProvider->getByMixed($parameters['route']);

                    if (null === $route) {
                        // if Route parameter was passed but it was not found - don't return articles not filtered by route
                        return false;
                    }
                }
            }

            if (null !== $route && (($route instanceof RouteInterface && RouteInterface::TYPE_COLLECTION === $route->getType()) || is_array($route))) {
                $criteria->set('route', $route);
            }

            if (isset($withoutParameters['route'])) {
                $criteria->set('exclude_route', $withoutParameters['route']);
            }

            foreach (['metadata', 'extra', 'keywords', 'source', 'author', 'article', 'publishedAfter', 'publishedBefore'] as $item) {
                if (isset($parameters[$item])) {
                    $criteria->set($item, $parameters[$item]);
                }

                if (isset($withoutParameters[$item])) {
                    $criteria->set('exclude_'.$item, $withoutParameters[$item]);
                }
            }

            $this->applyPaginationToCriteria($criteria, $parameters);
            $this->setDateRangeToCriteria($criteria, $parameters);
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

            return false;
        }

        return false;
    }

    public function isSupported(string $type): bool
    {
        return in_array($type, ['articles', 'article']);
    }

    protected function setDateRangeToCriteria(Criteria $criteria, array $parameters): void
    {
        if (isset($parameters['date_range']) && is_array($parameters['date_range']) && 2 === count($parameters['date_range'])) {
            $criteria->set('dateRange', $parameters['date_range']);
        }
    }

    protected function getArticleMeta(?ArticleInterface $article)
    {
        if (null !== $article) {
            $this->cacheBlockTagsCollector->addTagToCurrentCacheBlock('a-'.$article->getId());

            return $this->metaFactory->create($article);
        }

        return false;
    }
}
