<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AutorizationController extends Controller
{
    const INSTANT_ARTICLES_SCOPES = ['pages_manage_instant_articles', 'pages_show_list'];

    /**
     * @Route("/facebook/instantarticles/authorize/{appId}/{pageId}", options={"expose"=true}, name="swp_fbia_authorize")
     * @Method("GET")
     */
    public function authorizeAction($appId, $pageId)
    {
        $facebookApplication = $this->container->get('swp.repository.facebook_application')
            ->findOneBy(['appId' => $appId]);

        if (null === $facebookApplication) {
            throw new \Exception('Application with provided id don\'t exists.');
        }

        $facebook = $this->container->get('swp_facebook.manager')->createForApp($facebookApplication);

        $url = $facebook->getRedirectLoginHelper()->getLoginUrl(
            $this->generateUrl(
                'swp_fbia_authorize_callback',
                ['appId' => $facebookApplication->getAppId(), 'pageId' => $pageId], UrlGeneratorInterface::ABSOLUTE_URL
            ),
            self::INSTANT_ARTICLES_SCOPES);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/facebook/instantarticles/authorize/callback/{appId}/{pageId}", options={"expose"=true}, name="swp_fbia_authorize_callback")
     * @Method("GET|POST")
     */
    public function authorizationCallbackAction($appId, $pageId)
    {
        $facebookApplication = $this->get('swp.repository.facebook_application')->findOneBy(['appId' => $appId]);
        if (null === $facebookApplication) {
            throw new \Exception('Application with provided id don\'t exists.');
        }

        /** @var PageInterface $page */
        $page = $this->get('swp.repository.facebook_page')->findOneBy(['pageId' => $pageId]);
        if (null === $page) {
            throw new \Exception('Page with provided id don\'t exists.');
        }

        $facebookInstantArticlesManager = $this->container->get('swp_facebook.instant_articles_manager');
        $pageToken = $facebookInstantArticlesManager->getPageAccessToken(
            $this->container->get('swp_facebook.manager')->createForApp($facebookApplication),
            $pageId
        );

        if (null === $pageToken) {
            throw new \Exception('Your account don\'t have access to requested page.');
        }

        $page->setAccessToken($pageToken);
        $page->setApplication($facebookApplication);
        $this->container->get('swp.object_manager.facebook_page')->flush();

        return new JsonResponse([
            'pageId' => $pageId,
            'accessToken' => $pageToken,
        ]);
    }
}
