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

use SWP\Bundle\UserBundle\Event\FormEvent;
use SWP\Bundle\UserBundle\Event\GetResponseUserEvent;
use SWP\Bundle\UserBundle\SWPUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResettingListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var int
     */
    private $tokenTtl;

    /**
     * ResettingListener constructor.
     *
     * @param int $tokenTtl
     */
    public function __construct(UrlGeneratorInterface $router, $tokenTtl)
    {
        $this->router = $router;
        $this->tokenTtl = $tokenTtl;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SWPUserEvents::RESETTING_RESET_INITIALIZE => 'onResettingResetInitialize',
            SWPUserEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess',
            SWPUserEvents::RESETTING_RESET_REQUEST => 'onResettingResetRequest',
        ];
    }

    public function onResettingResetInitialize(GetResponseUserEvent $event)
    {
        if (!$event->getUser()->isPasswordRequestNonExpired($this->tokenTtl)) {
            $event->setResponse(new RedirectResponse($this->router->generate('swp_user_resetting_request')));
        }
    }

    public function onResettingResetSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
    }

    public function onResettingResetRequest(GetResponseUserEvent $event)
    {
        if (!$event->getUser()->isAccountNonLocked()) {
            $event->setResponse(new RedirectResponse($this->router->generate('swp_user_resetting_request')));
        }
    }
}
