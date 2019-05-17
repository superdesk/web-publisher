<?php

namespace SWP\Bundle\CoreBundle\Controller;

class OauthController extends Controller
{
    /**
     * @Route("/connect/oauth", name="connect_oauth_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('oatuh_client')
            ->redirect([
                'openid', 'email'
            ]);
    }

    /**
     * @Route("/connect/oauth/check", name="connect_oauth_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('oauth_client');

        try {
            $oauthUser = $client->fetchUser();
        } catch(IdentityProvderException $e) {
            var_dump($e->getMessage()); die;
        }
    }
}
