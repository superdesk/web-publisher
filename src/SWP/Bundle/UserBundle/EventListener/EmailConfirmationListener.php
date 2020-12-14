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
use SWP\Bundle\UserBundle\Mailer\MailerInterface;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\SWPUserEvents;
use SWP\Bundle\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailConfirmationListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;

    /**
     * EmailConfirmationListener constructor.
     */
    public function __construct(MailerInterface $mailer,
                                TokenGeneratorInterface $tokenGenerator,
                                UrlGeneratorInterface $router,
                                SessionInterface $session
    )
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SWPUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        /** @var UserInterface $user */
        $user = $event->getForm()->getData();

        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->mailer->sendConfirmationEmailMessage($user);

        $this->session->set('swp_user_send_confirmation_email/email', $user->getEmail());

        $url = $this->router->generate('swp_user_registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }
}
