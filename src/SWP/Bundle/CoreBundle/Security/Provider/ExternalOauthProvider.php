<?php

namespace SWP\Bundle\CoreBundle\Security\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Token\AccessToken;


class ExternalOauthProvider extends AbstractProvider
{
    public function getBaseAuthorizationUrl()
    {
        return 'https://dev-sxtg2-xy.eu.auth0.com/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://dev-sxtg2-xy.eu.auth0.com/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://dev-sxtg2-xy.eu.auth0.com/userinfo';
    }

    protected function getDefaultScopes()
    {
        return ['openid', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if($response->getStatusCode() >= 400) {
            return new IdentifyProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                (string) $response->getBody());
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ExternalOauthResourceOwner($response);
    }
}
