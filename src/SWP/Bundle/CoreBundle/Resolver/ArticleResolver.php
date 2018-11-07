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

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class ArticleResolver implements ArticleResolverInterface
{
    /**
     * @var UrlMatcherInterface
     */
    private $matcher;

    public function __construct(UrlMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    public function resolve(string $url): ?ArticleInterface
    {
        try {
            $route = $this->matcher->match($this->getFragmentFromUrl($url, 'path'));
            if (isset($route['_article_meta']) && $route['_article_meta']->getValues() instanceof ArticleInterface) {
                return $route['_article_meta']->getValues();
            }
        } catch (ResourceNotFoundException $e) {
            return null;
        }
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
