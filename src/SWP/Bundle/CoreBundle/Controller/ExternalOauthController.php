<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use SWP\Bundle\CoreBundle\Exception\ExternalOauthException;

class ExternalOauthController extends Controller
{
    /**
     * @Route("/connect/oauth", name="connect_oauth_start")
     */
    public function connectAction(Request $request)
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
    public function connectCheckAction(Request $request)
    {
        $clientRegistry = $this->get('knpu.oauth2.registry');
        $client = $clientRegistry->getClient('external_oauth');

        $accessToken = $client->getAccessToken();
        $oauthUser = $client->fetchUserFromToken($accessToken);

        try {
            $user = $this->getUser();
        } catch(ExternalOauthException $e) {
                return new JsonResponse(array('exception' => true));
        }

        if(!$user) {
            return new JsonResponse(array('status' => false, 'message' => "User not found and failed to create new user!"));
        }

        return new JsonResponse(array('user' => $user->getEmail()));

        # Redirect to /
        $response = $this->redirectToRoute('homepage');

        return $response;
    }
}
