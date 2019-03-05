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
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

final class SetArticlePublishDateListener
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

    public function setPublishDate(ArticleEvent $event): void
    {
        $article = $event->getArticle();
        $package = $event->getPackage();

        $useFirstPublished = $this->settingsManager->get('use_first_published_as_publish_date', 'tenant', $this->tenantContext->getTenant());
        if ($useFirstPublished && null !== ($firstPublishedAt = $package->getFirstPublishedAt())) {
            $firstPublishedAt->setTimezone(new \DateTimeZone('UTC'));

            $article->setPublishedAt($firstPublishedAt);
        }
    }
}
