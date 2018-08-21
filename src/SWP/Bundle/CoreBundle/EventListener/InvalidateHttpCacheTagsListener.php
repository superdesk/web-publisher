<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\EventListener;

use FOS\HttpCacheBundle\CacheManager;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheArticleTagGeneratorInterface;

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
