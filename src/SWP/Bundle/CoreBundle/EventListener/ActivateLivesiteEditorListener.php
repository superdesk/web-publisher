<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ActivateLivesiteEditorListener.
 */
class ActivateLivesiteEditorListener
{
    const ACTIVATION_KEY = 'activate_livesite_editor';

    /**
     * @var TokenStorageInterface
     */
    protected $securityTokenStorage;

    /**
     * ActivateLivesiteEditorListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->securityTokenStorage = $tokenStorage;
    }

    /**
     * Check if request have attribute for Livesite Editor activation.
     * Value of this attribute should be valid auth token - it will be set as value of activation cookie.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->attributes->has(self::ACTIVATION_KEY)) {
            $token = $this->securityTokenStorage->getToken();
            if ($token instanceof TokenInterface) {
                $user = $token->getUser();
                if (!$user instanceof UserInterface || !$user->hasRole('ROLE_LIVESITE_EDITOR')) {
                    return;
                }
            }

            $response = $event->getResponse();
            $response->headers->setCookie(new Cookie(self::ACTIVATION_KEY, $request->attributes->get(self::ACTIVATION_KEY)));

            $event->setResponse($response);
        }
    }
}
