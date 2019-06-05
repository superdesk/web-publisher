<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Resolver;

use Doctrine\Common\Cache\CacheProvider;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class ArticleResolver implements ArticleResolverInterface
{
    /**
     * @var UrlMatcherInterface
     */
    private $matcher;

    private $cacheProvider;

    public function __construct(UrlMatcherInterface $matcher, CacheProvider $cacheProvider)
    {
        $this->matcher = $matcher;
        $this->cacheProvider = $cacheProvider;
    }

    public function resolve(string $url): ?ArticleInterface
    {
        $collectionRouteCacheKey = md5('route_'.$url);
        $articleCacheKey = md5('article_'.$url);

        if ($this->cacheProvider->contains($articleCacheKey)) {
            return $this->cacheProvider->fetch($articleCacheKey);
        }

        if ($this->cacheProvider->contains($collectionRouteCacheKey)) {
            return null;
        }

        try {
            $route = $this->matcher->match($this->getFragmentFromUrl($url, 'path'));

            if (isset($route['_article_meta']) && $route['_article_meta'] instanceof Meta && ($article = $route['_article_meta']->getValues()) instanceof ArticleInterface) {
                $this->cacheProvider->save($articleCacheKey, $article);

                return $article;
            }

            if (isset($route['_route_meta']) &&
                $route['_route_meta'] instanceof Meta &&
                $route['_route_meta']->getValues() instanceof RouteInterface &&
                RouteInterface::TYPE_COLLECTION === $route['_route_meta']->getValues()->getType()
            ) {
                $this->cacheProvider->save($collectionRouteCacheKey, null);
            }
        } catch (ResourceNotFoundException $e) {
            return null;
        }

        return null;
    }

    private function getFragmentFromUrl(string $url, string $fragment): ?string
    {
        $fragments = \parse_url($url);
        if (!\array_key_exists($fragment, $fragments)) {
            return null;
        }

        return str_replace('/app_dev.php', '', $fragments[$fragment]);
    }
}
