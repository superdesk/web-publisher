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
        return getenv('EXTERNAL_OAUTH_BASE_URL') . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return getenv('EXTERNAL_OAUTH_BASE_URL') . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return getenv('EXTERNAL_OAUTH_BASE_URL') . '/userinfo?access_token='.$token;
    }

    protected function getDefaultScopes()
    {
        return ['openid', 'profile', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if($response->getStatusCode() >= 400) {
            return new IdentityProviderException(
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
