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

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
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

    /**
     * @var CacheInterface
     */
    private $cacheProvider;

    public function __construct(UrlMatcherInterface $matcher, CacheInterface $cacheProvider)
    {
        $this->matcher = $matcher;
        $this->cacheProvider = $cacheProvider;
    }

    public function resolve(string $url): ?ArticleInterface
    {
        $collectionRouteCacheKey = md5('route_'.$url);
        return $this->cacheProvider->get($collectionRouteCacheKey, function (ItemInterface $item, &$save) use ($url) {
          try {
            $route = $this->matcher->match($this->getFragmentFromUrl($url, 'path'));
          } catch(ResourceNotFoundException $e) {
            $save = false;
            return null;
          }
          if(!isset($route['_article_meta'])) {
            $save = false;
            return null;
          }

          if(!($route['_article_meta'] instanceof Meta)) {
            $save = false;
            return null;
          }

          $values = $route['_article_meta']->getValues();
          if(!($values instanceof ArticleInterface)) {
            $save = false;
            return null;
          }

          return $values;
        });
    }

    private function getFragmentFromUrl(string $url, string $fragment): ?string
    {
        $fragments = \parse_url($url);
        if (!\array_key_exists($fragment, $fragments)) {
            return null;
        }

        return $fragments[$fragment];
    }
}
