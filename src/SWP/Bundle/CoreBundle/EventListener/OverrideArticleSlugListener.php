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

    public function overrideSlugIfNeeded(ArticleEvent $event): void
    {
        $article = $event->getArticle();
        $package = $event->getPackage();

        $overrideSlugOnCorrection = $this->settingsManager->get('override_slug_on_correction', 'tenant', $this->tenantContext->getTenant());

        if ($overrideSlugOnCorrection && null !== $article->getSlug()) {
            $this->savePreviousRelativeUrl($article);
            $article->setSlug($package->getSlugline() ?? Transliterator::urlize($article->getTitle()));
        }
    }

    private function savePreviousRelativeUrl(ArticleInterface $article): void
    {
        $route = $article->getRoute();
        $relativeUrl = new ArticlePreviousRelativeUrl();
        $relativeUrl->setRelativeUrl($this->router->generate($route->getName(), ['slug' => $article->getSlug()]));

        $article->addPreviousRelativeUrl($relativeUrl);
    }
}
