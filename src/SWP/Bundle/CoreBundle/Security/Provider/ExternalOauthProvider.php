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

    public function getAccessToken($grant, array $options = []): AccessToken
    {
        if (!isset($this->access_token)) {
            $this->access_token = parent::getAccessToken($grant, $options);
        }

        return $this->access_token;
    }

    public function getBaseAuthorizationUrl(): string
    {
        return $this->base_url.'/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->base_url.'/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->base_url.'/userinfo';
    }

    protected function getDefaultScopes(): string
    {
        return ['openid', 'profile', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $response->getReasonPhrase(),
                $response->getStatusCode(),
                (string) $response->getBody());
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): ExternalOauthResourceOwner
    {
        return new ExternalOauthResourceOwner($response);
    }

    protected function getScopeSeparator(): string
    {
        return $this->scope_separator;
    }

    protected function getAuthorizationHeaders($token = null): array
    {
        if ($token) {
            return [
                'Authorization' => 'Bearer '.$token,
            ];
        }

        return [];
    }
}
