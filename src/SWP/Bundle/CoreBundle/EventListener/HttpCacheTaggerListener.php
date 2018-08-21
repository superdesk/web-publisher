<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\EventListener;

use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheArticleTagGeneratorInterface;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheRouteTagGeneratorInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HttpCacheTaggerListener
{
    /**
     * @var SymfonyResponseTagger
     */
    private $responseTagger;

    /**
     * @var HttpCacheArticleTagGeneratorInterface
     */
    private $articleTagGenerator;

    /**
     * @var HttpCacheRouteTagGeneratorInterface
     */
    private $routeTagGenerator;

    public function __construct(
        SymfonyResponseTagger $responseTagger,
        HttpCacheArticleTagGeneratorInterface $articleTagGenerator,
        HttpCacheRouteTagGeneratorInterface $routeTagGenerator
    ) {
        $this->responseTagger = $responseTagger;
        $this->articleTagGenerator = $articleTagGenerator;
        $this->routeTagGenerator = $routeTagGenerator;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var ArticleInterface $article */
        $article = $event->getRequest()->get(DynamicRouter::CONTENT_KEY);

        if (null !== $article) {
            $this->responseTagger->addTags([$this->articleTagGenerator->generateTag($article)]);

            return;
        }

        /** @var RouteInterface $routeObject */
        $routeObject = $event->getRequest()->get(DynamicRouter::ROUTE_KEY);

        if (null !== $routeObject) {
            $this->responseTagger->addTags([$this->routeTagGenerator->generateTag($routeObject)]);
        }
    }
}
