<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ExternalOauthController extends Controller
{
    public const PUBLISHER_JWT_COOKIE = 'publisher_jwt';
    public const PUBLISHER_REDIRECT_TO_AFTER_OAUTH = 'publisher_redirect_to_after_oauth';

    /**
     * @Route("/connect/oauth", name="connect_oauth_start")
     */
    public function connectAction(Request $request, SessionInterface $session): Response
    {
        if ($request->query->has("redirect_to_route_when_successful")) {
            $session->set(
                self::PUBLISHER_REDIRECT_TO_AFTER_OAUTH,
                $request->query->get("redirect_to_route_when_successful")
           );
        }

        $clientRegistry = $this->get('knpu.oauth2.registry');

        return $clientRegistry
            ->getClient('external_oauth')
            ->redirect([
                'openid', 'email', 'profile',
            ]);
    }

    /**
     * This is where the user is redirected after being succesfully authenticated by the OAuth server.
     *
     * @Route("/connect/oauth/check", name="connect_oauth_check")
     */
    public function connectCheckAction(JWTTokenManagerInterface $jwtTokenManager, SessionInterface $session): Response
    {
        // If we didn't log in, something went wrong. Throw an exception!
        if (!($user = $this->getUser())) {
            $response = $this->render('bundles/TwigBundle/Exception/error403.html.twig');
            $response->setStatusCode(403);

            return $response;
        }

        if ($session->has(self::PUBLISHER_REDIRECT_TO_AFTER_OAUTH)) {
            $redirectRoute = $session->remove(self::PUBLISHER_REDIRECT_TO_AFTER_OAUTH);
            $response = $this->redirect($redirectRoute);
        } else {
            $response = $this->redirectToRoute('homepage');
        }
        
        $response->headers->setCookie(Cookie::create(self::PUBLISHER_JWT_COOKIE, $jwtTokenManager->create($user)));

        return $response;
    }
}
