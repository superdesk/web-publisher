<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Behat\Transliterator\Transliterator;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticlePreviousRelativeUrl;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Routing\RouterInterface;

final class OverrideArticleSlugListener
{
    private $settingsManager;

    private $tenantContext;

    private $router;

    public function __construct(
        SettingsManagerInterface $settingsManager,
        TenantContextInterface $tenantContext,
        RouterInterface $router
    ) {
        $this->settingsManager = $settingsManager;
        $this->tenantContext = $tenantContext;
        $this->router = $router;
    }

    /**
     * We save previous url in two cases:
     *  - when slug is changed, for example: `/politics/my-example-article` => `/politics/my-changed-example-article`
     *  - when base route (category) is changed: `/politics/my-example-article` => `/sports/my-example-article`
     *
     * Please consider that both can be true in same request.
     *
     * @param ArticleEvent $event
     * @return void
     */
    public function overrideSlugIfNeeded(ArticleEvent $event): void
    {
        $article = $event->getArticle();
        $package = $event->getPackage();
        $previousRoute = $event->getPreviousRoute();

        $overrideSlugOnCorrection = $this->settingsManager->get('override_slug_on_correction', 'tenant', $this->tenantContext->getTenant());

        if ($overrideSlugOnCorrection && null !== $article->getSlug()) {
            $this->savePreviousRelativeUrl($article, $previousRoute);
            $article->setSlug($package->getSlugline() ?? Transliterator::urlize($article->getTitle()));
        } else if ($previousRoute) {
            $this->savePreviousRelativeUrl($article, $previousRoute);
        }
    }

    private function savePreviousRelativeUrl(ArticleInterface $article, RouteInterface $route = null): void
    {
        $route = $route ?? $article->getRoute();
        $relativeUrl = new ArticlePreviousRelativeUrl();
        $relativeUrl->setRelativeUrl($this->router->generate($route->getName(), ['slug' => $article->getSlug()]));

        $article->addPreviousRelativeUrl($relativeUrl);
    }
}
