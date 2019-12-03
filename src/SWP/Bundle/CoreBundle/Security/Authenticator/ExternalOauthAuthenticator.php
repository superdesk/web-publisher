<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Security\Authenticator;

use Symfony\Component\Security\Core\User\UserInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Security;
use League\OAuth2\Client\Token\AccessToken;
use function uniqid;

class ExternalOauthAuthenticator extends SocialAuthenticator
{
    protected $clientRegistry;

    protected $um;

    protected $security;

    public function __construct(
        ClientRegistry $clientRegistry,
        UserManagerInterface $um,
        Security $security
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->um = $um;
        $this->security = $security;
    }

    public function supports(Request $request): bool
    {
        if (($request->query->get('code') && $request->get('state')) && !$this->security->getUser()) {
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getOauthClient());
    }

    /**
     * Get the user given the access token. If the user exists as a local user,
     * fetch that one, if it does not, create a new user using the OAuth user
     * fetched from the resource server using the access token.
     *
     * @inehritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        // Fetch the user from the resource server
        $oauthUser = $this->getOauthClient()->fetchUserFromToken($credentials);
        $oauthEmail = $oauthUser->getEmail();
        $oauthId = $oauthUser->getId();

        if (!$oauthUser) {
            return null;
        }

        // Is there an existing user with the same oauth id?
        /** @var \SWP\Bundle\CoreBundle\Model\UserInterface $user */
        $user = $userProvider->findOneByExternalId($oauthId);
        if ($user) {
            if ($user->getEmail() !== $oauthEmail) {
                // If the email has changed for the user, update it here as well
                $user->setEmail($oauthEmail);
                $user->setUsername($oauthEmail);
                $this->um->updateUser($user);
            }

            return $user;
        }

        // Is there an existing user with the same email address?
        $user = $userProvider->findOneByEmail($oauthEmail);
        if ($user) {
            return $user;
        }

        // No user found, create one using the user info provided by resource server
        $user = $this->um->createUser();
        $user->setEmail($oauthEmail);
        $user->setUsername($oauthEmail);
        $user->setExternalId($oauthId);
        $user->setPassword(uniqid('', true));
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->addRole('ROLE_USER');

        $this->um->updateUser($user);

        return $user;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): void
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): void
    {
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/oauth/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getOauthClient(): OAuth2Client
    {
        return $this->clientRegistry->getClient('external_oauth');
    }
}
