<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\EventSubscriber;

use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ScopeContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var ScopeContextInterface
     */
    protected $scopeContext;

    /**
     * @var TokenStorageInterface
     */
    protected $securityTokenStorage;

    /**
     * ScopeContextSubscriber constructor.
     *
     * @param ScopeContextInterface $scopeContext
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(ScopeContextInterface $scopeContext, TokenStorageInterface $tokenStorage)
    {
        $this->scopeContext = $scopeContext;
        $this->securityTokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $token = $this->securityTokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
            if ($user instanceof UserInterface && $user instanceof SettingsOwnerInterface) {
                $this->scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_USER, $user);
            }
        }
    }
}
