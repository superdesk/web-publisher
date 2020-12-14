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

use SWP\Bundle\UserBundle\SWPUserEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static $successMessages = [
        SWPUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        SWPUserEvents::GROUP_CREATE_COMPLETED => 'group.flash.created',
        SWPUserEvents::GROUP_DELETE_COMPLETED => 'group.flash.deleted',
        SWPUserEvents::GROUP_EDIT_COMPLETED => 'group.flash.updated',
        SWPUserEvents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        SWPUserEvents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        SWPUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    ];

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FlashListener constructor.
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SWPUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            SWPUserEvents::GROUP_CREATE_COMPLETED => 'addSuccessFlash',
            SWPUserEvents::GROUP_DELETE_COMPLETED => 'addSuccessFlash',
            SWPUserEvents::GROUP_EDIT_COMPLETED => 'addSuccessFlash',
            SWPUserEvents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            SWPUserEvents::REGISTRATION_COMPLETED => 'addSuccessFlash',
            SWPUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        ];
    }

    /**
     * @param string $eventName
     */
    public function addSuccessFlash(Event $event, $eventName)
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    /**
     * @param string$message
     *
     * @return string
     */
    private function trans($message, array $params = [])
    {
        return $this->translator->trans($message, $params, 'SWPUserBundle');
    }
}
