<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ExternalOauthController extends Controller
{
    public const PUBLISHER_JWT_COOKIE = 'publisher_jwt';

    /**
     * @Route("/connect/oauth", name="connect_oauth_start")
     */
    public function connectAction(Request $request): Response
    {
        $referer = $request->headers->get('referer');

        $clientRegistry = $this->get('knpu.oauth2.registry');

        return $clientRegistry
            ->getClient('external_oauth')
            ->redirect([
                'openid', 'email', 'profile',
            ], ["state" => $referer]);
    }

    /**
     * This is where the user is redirected after being succesfully authenticated by the OAuth server.
     *
     * @Route("/connect/oauth/check", name="connect_oauth_check")
     */
    public function connectCheckAction(Request $request, JWTTokenManagerInterface $jwtTokenManager): Response
    {
        // If we didn't log in, something went wrong. Throw an exception!
        if (!($user = $this->getUser())) {
            $response = $this->render('bundles/TwigBundle/Exception/error403.html.twig');
            $response->setStatusCode(403);

            return $response;
        }

        $state = $request->query->get('state');
        $response = $this->redirect($state);
        
        $response->headers->setCookie(Cookie::create(self::PUBLISHER_JWT_COOKIE, $jwtTokenManager->create($user)));

        return $response;
    }
}
