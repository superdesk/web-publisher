<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use FOS\HttpCache\Exception\ExceptionCollection;
use FOS\HttpCacheBundle\CacheManager;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheArticleTagGeneratorInterface;
use SWP\Bundle\CoreBundle\HttpCache\HttpCacheRouteTagGeneratorInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HttpCacheSubscriber implements EventSubscriberInterface
{
    protected $cacheManager;

    protected $logger;

    protected $tenantContext;

    private $articleTagGenerator;

    private $routeTagGenerator;

    public function __construct(
        CacheManager $cacheManager,
        LoggerInterface $logger,
        TenantContextInterface $tenantContext,
        HttpCacheArticleTagGeneratorInterface $articleTagGenerator,
        HttpCacheRouteTagGeneratorInterface $routeTagGenerator
    ) {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
        $this->tenantContext = $tenantContext;
        $this->articleTagGenerator = $articleTagGenerator;
        $this->routeTagGenerator = $routeTagGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ArticleEvents::POST_UPDATE => 'clearCache',
        ];
    }

    public function clearCache(ArticleEvent $event): void
    {
        $headers = ['host' => $this->getHostName($this->tenantContext->getTenant())];

        $article = $event->getArticle();
        if (
            null !== $article->getId()
        ) {
            $tags = $this->articleTagGenerator->generateTags($article);
            // Clear article route page (usually article is listed there)
            if (null !== $article->getRoute()) {
                $tags = array_merge($tags, $this->routeTagGenerator->generateTags($article->getRoute()));
                if (null !== $article->getRoute()->getParent()) {
                    $tags = array_merge($this->routeTagGenerator->generateTags($article->getRoute()->getParent()));
                }
            }
            $this->cacheManager->invalidateTags($tags);

            // Invalidate API responses
            $this->cacheManager->invalidateRoute('swp_api_content_list_articles', [], $headers);
            $this->cacheManager->invalidateRoute('swp_api_content_show_articles', ['id' => $article->getId()], $headers);

            // To be sure that we have fresh front page - clear cache also there
            $this->cacheManager->invalidateRoute('homepage', [], $headers);
        }

        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            $this->logger->error($e->getMessage());
        }
    }

    private function getHostName(TenantInterface $tenant): string
    {
        $hostName = $tenant->getDomainName();

        if (null !== $tenant->getSubdomain()) {
            $hostName = $tenant->getSubdomain().'.'.$hostName;
        }

        return $hostName;
    }
}
