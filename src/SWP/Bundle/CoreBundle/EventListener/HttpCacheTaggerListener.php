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

use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheArticleTagGeneratorInterface;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheRouteTagGeneratorInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpCacheTaggerListener
{
    private $responseTagger;

    private $articleTagGenerator;

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

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var ArticleInterface $article */
        $article = $event->getRequest()->get(DynamicRouter::CONTENT_KEY);
        if (null !== $article) {
            $this->responseTagger->addTags($this->articleTagGenerator->generateTags($article));

            return;
        }

        /** @var RouteInterface $routeObject */
        $routeObject = $event->getRequest()->get(DynamicRouter::ROUTE_KEY);
        if (null !== $routeObject) {
            $this->responseTagger->addTags($this->routeTagGenerator->generateTags($routeObject));
        }
    }
}
