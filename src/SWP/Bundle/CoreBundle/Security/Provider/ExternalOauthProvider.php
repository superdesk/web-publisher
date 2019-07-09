<?php

namespace SWP\Bundle\CoreBundle\Security\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Token\AccessToken;


class ExternalOauthProvider extends AbstractProvider
{
    protected $base_url;
    protected $scope_separator;
    protected $access_token;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->base_url = $options['base_url'];
        $this->scope_separator = $options['scope_separator'];
    }

    public function getAccessToken($grant, $options = []) {
        if(!isset($this->access_token)) {
            $this->access_token = parent::getAccessToken($grant, $options);
        }
        return $this->access_token;
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->base_url . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->base_url . '/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->base_url . '/userinfo';
    }

    protected function getDefaultScopes()
    {
        return ['openid', 'profile', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if($response->getStatusCode() >= 400) {
            return new IdentityProviderException(
                $response->getReasonPhrase(),
                $response->getStatusCode(),
                (string) $response->getBody());
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ExternalOauthResourceOwner($response);
    }

    protected function getScopeSeparator()
    {
        return $this->scope_separator;
    }

    protected function getAuthorizationHeaders($token = null)
    {
        if($token) {
            return [
                'Authorization' => 'Bearer ' . $token
            ];
        } else {
            return [];
        }
    }
}
