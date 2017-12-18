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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ActivateLivesiteEditorListener.
 */
class ActivateLivesiteEditorListener
{
    const ACTIVATION_KEY = 'activate_livesite_editor';

    const APPEND_SCRIPTS = 'append_livesite_editor_scripts';

    /**
     * @var TokenStorageInterface
     */
    protected $securityTokenStorage;

    /**
     * @var string
     */
    protected $env;

    /**
     * ActivateLivesiteEditorListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param string                $env
     */
    public function __construct(TokenStorageInterface $tokenStorage, string $env)
    {
        $this->securityTokenStorage = $tokenStorage;
        $this->env = $env;
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
        $response = $event->getResponse();
        if ($request->attributes->has(self::ACTIVATION_KEY)) {
            $token = $this->securityTokenStorage->getToken();
            if ($token instanceof TokenInterface) {
                $user = $token->getUser();
                if (!$user instanceof UserInterface || !$user->hasRole('ROLE_LIVESITE_EDITOR')) {
                    return;
                }
            }

            $response->headers->setCookie(new Cookie(self::ACTIVATION_KEY, $request->attributes->get(self::ACTIVATION_KEY), 0, '/', null, false, false));
            $response->headers->setCookie(new Cookie(self::APPEND_SCRIPTS, true));
        }

        $this->injectScripts($response, $request);
    }

    /**
     * @param Response $response
     * @param Request  $request
     */
    protected function injectScripts(Response $response, Request $request)
    {
        if (
            null === $request->cookies->get(self::APPEND_SCRIPTS, null) ||
            false !== strpos($request->get('_route', ''), '_profiler') ||
            false !== strpos($request->get('_route', ''), 'swp_api')
        ) {
            return;
        }

        $content = $response->getContent();
        $content = str_replace('<html ', '<html ng-app="livesite-management" ', $content);
        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $toolbar = "\n".str_replace("\n", '', $this->getLivesiteEditorScript($this->env))."\n";
            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * @param string $env
     *
     * @return string
     */
    private function getLivesiteEditorScript(string $env)
    {
        $script = 'prod' === $env ? 'app.js' : 'app_dev.js';

        return '<script src="/livesite_editor/assets/'.$script.'"></script>';
    }
}
