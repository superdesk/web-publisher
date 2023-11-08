<?php

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Model\ArticleExtraTextField;
use SWP\Bundle\CoreBundle\Util\SwpLogger;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;

final class DontChangeArticleUpdateAtListener
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

    public function setUpdateAtDate(ArticleEvent $event): void
    {
        $article = $event->getArticle();
        /**
         * @var ?ArticleExtraTextField $dontUpdateDate
         */
        $dontUpdateDate = $article->getExtraByKey('dont_change_updated_at') ?? null;
        if ($dontUpdateDate instanceof ArticleExtraTextField && strtolower($dontUpdateDate->getValue()) === 'on') {
            $article->cancelTimestampable();
        }
    }
}
