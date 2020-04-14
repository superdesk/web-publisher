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

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSlug;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

final class OverrideArticleSlugListener
{
    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    public function __construct(SettingsManagerInterface $settingsManager, TenantContextInterface $tenantContext)
    {
        $this->settingsManager = $settingsManager;
        $this->tenantContext = $tenantContext;
    }

    public function overrideSlugIfNeeded(ArticleEvent $event): void
    {
        $article = $event->getArticle();
        $package = $event->getPackage();

        $overrideSlugOnCorrection = $this->settingsManager->get('override_slug_on_correction', 'tenant', $this->tenantContext->getTenant());

        if ($overrideSlugOnCorrection && null !== $article->getSlug()) {
            $this->savePreviousSlug($article);
            $article->setSlug($package->getSlugline());
        }
    }

    private function savePreviousSlug(ArticleInterface $article): void
    {
        $articleSlug = new ArticleSlug();
        $articleSlug->setSlug($article->getSlug());

        $article->addSlug($articleSlug);
    }
}
