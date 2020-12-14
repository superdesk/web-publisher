<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\EventListener;

use SWP\Bundle\UserBundle\Event\FilterUserResponseEvent;
use SWP\Bundle\UserBundle\Event\UserEvent;
use SWP\Bundle\UserBundle\SWPUserEvents;
use SWP\Bundle\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * @var LoginManagerInterface
     */
    private $loginManager;

    /**
     * @var string
     */
    private $firewallName;

    /**
     * AuthenticationListener constructor.
     *
     * @param string $firewallName
     */
    public function __construct(LoginManagerInterface $loginManager, $firewallName)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewallName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SWPUserEvents::REGISTRATION_COMPLETED => 'authenticate',
            SWPUserEvents::REGISTRATION_CONFIRMED => 'authenticate',
            SWPUserEvents::RESETTING_RESET_COMPLETED => 'authenticate',
        ];
    }

    /**
     * @param string $eventName
     */
    public function authenticate(FilterUserResponseEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
        try {
            $this->loginManager->logInUser($this->firewallName, $event->getUser(), $event->getResponse());

            $eventDispatcher->dispatch(new UserEvent($event->getUser(), $event->getRequest()), SWPUserEvents::SECURITY_IMPLICIT_LOGIN);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
