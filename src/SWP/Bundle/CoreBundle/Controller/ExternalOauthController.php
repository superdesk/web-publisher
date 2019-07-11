<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ExternalOauthController extends Controller
{
    /**
     * @Route("/connect/oauth", name="connect_oauth_start")
     */
    public function connectAction(Request $request): Response
    {
        $clientRegistry = $this->get('knpu.oauth2.registry');

        return $clientRegistry
            ->getClient('external_oauth')
            ->redirect([
                'openid', 'email', 'profile'
            ]);
    }

    /**
     * This is where the user is redirected after being succesfully authenticated by the OAuth server.
     * @Route("/connect/oauth/check", name="connect_oauth_check")
     */
    public function connectCheckAction(Request $request): RedirectResponse
    {
        // Redirect to /
        $response = $this->redirectToRoute('homepage');

        return $response;
    }
}
