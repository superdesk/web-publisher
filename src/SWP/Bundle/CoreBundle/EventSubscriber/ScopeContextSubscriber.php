<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ScopeContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var ScopeContextInterface
     */
    protected $scopeContext;

    /**
     * @var ThemeContextInterface
     */
    protected $themeContext;

    /**
     * ScopeContextSubscriber constructor.
     *
     * @param ScopeContextInterface $scopeContext
     * @param ThemeContextInterface $themeContext
     */
    public function __construct(ScopeContextInterface $scopeContext, ThemeContextInterface $themeContext)
    {
        $this->scopeContext = $scopeContext;
        $this->themeContext = $themeContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MultiTenancyEvents::TENANT_SET => 'onTenantSet',
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function onTenantSet(GenericEvent $event)
    {
        $tenant = $event->getSubject();

        if ($tenant instanceof SettingsOwnerInterface) {
            $this->scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_TENANT, $tenant);
            $this->scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_ORGANIZATION, $tenant->getOrganization());

            if ($this->themeContext->getTheme() instanceof ThemeInterface) {
                $this->scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_THEME, $tenant);
            }
        }
    }
}
