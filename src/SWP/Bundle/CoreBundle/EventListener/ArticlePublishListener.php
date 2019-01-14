<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

final class ArticlePublishListener extends \SWP\Bundle\ContentBundle\EventListener\ArticlePublishListener
{
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ArticlePublishListener constructor.
     *
     * @param ArticleServiceInterface  $articleService
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(ArticleServiceInterface $articleService, SettingsManagerInterface $settingsManager, TenantContextInterface $tenantContext)
    {
        $this->articleService = $articleService;
        $this->settingsManager = $settingsManager;
        $this->tenantContext = $tenantContext;

        parent::__construct($articleService);
    }

    /**
     * @param ArticleEvent $event
     */
    public function publish(ArticleEvent $event)
    {
        $article = $event->getArticle();
        $package = $event->getPackage();

        $useFirstPublished = $this->settingsManager->get('use_first_published_as_publish_date', 'tenant', $this->tenantContext->getTenant());

        if ($useFirstPublished && null !== ($firstPublishedAt = $package->getFirstPublishedAt())) {
            $firstPublishedAt->setTimezone(new \DateTimeZone('UTC'));
            $article->setPublishedAt($firstPublishedAt);

            return;
        }

        if ($article->isPublished()) {
            return;
        }

        $this->articleService->publish($article);
    }
}
