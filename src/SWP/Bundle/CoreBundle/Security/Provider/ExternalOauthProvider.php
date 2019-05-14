<?php
use League\OAuth2\Client\Provider\AbstractProvider;

class ExternalOauthProvider extends AbstractProvider
{
    public function getBaseAuthorizationUrl()
    {
    }

    public function getBaseAccessTokenUrl(array $params)
    {
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
    }

    protected function getDefaultScopes()
    {
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
    }
}
