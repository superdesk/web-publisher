<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use FOS\HttpCacheBundle\CacheManager;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheArticleTagGeneratorInterface;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheRouteTagGeneratorInterface;

final class InvalidateHttpCacheTagsListener
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var HttpCacheArticleTagGeneratorInterface
     */
    private $articleTagGenerator;

    /**
     * @var HttpCacheArticleTagGeneratorInterface
     */
    private $routeTagGenerator;

    public function __construct(
        CacheManager $cacheManager,
        HttpCacheArticleTagGeneratorInterface $articleTagGenerator,
        HttpCacheRouteTagGeneratorInterface $routeTagGenerator
    ) {
        $this->cacheManager = $cacheManager;
        $this->articleTagGenerator = $articleTagGenerator;
        $this->routeTagGenerator = $routeTagGenerator;
    }

    public function onPostUpdate(ArticleEvent $event): void
    {
        $article = $event->getArticle();

        $tags = [
            $this->articleTagGenerator->generateTag($article),
        ];

        if (null !== $article->getRoute()) {
            $tags[] = $this->routeTagGenerator->generateTag($article->getRoute());
        }

        $this->cacheManager->invalidateTags($tags);
    }
}
