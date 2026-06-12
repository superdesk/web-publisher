<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Security\Authenticator;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use SWP\Bundle\CoreBundle\Security\Provider\UserProvider;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use function uniqid;

class ExternalOauthAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    protected $clientRegistry;

    protected $um;

    protected $security;

    protected $userProvider;

    public function __construct(
        ClientRegistry $clientRegistry,
        UserManagerInterface $um,
        Security $security,
        UserProvider $userProvider
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->um = $um;
        $this->security = $security;
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        if (($request->query->get('code') && $request->get('state')) && !$this->security->getUser()) {
            return true;
        }

        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->fetchAccessToken($this->getOauthClient());

        return new SelfValidatingPassport(new UserBadge($accessToken->getToken(), function () use ($accessToken) {
            // Fetch the user from the resource server
            $oauthUser = $this->getOauthClient()->fetchUserFromToken($accessToken);
            $oauthEmail = $oauthUser->getEmail();
            $oauthId = $oauthUser->getId();

            // Is there an existing user with the same oauth id?
            /** @var \SWP\Bundle\CoreBundle\Model\UserInterface|null $user */
            $user = $this->userProvider->findOneByExternalId($oauthId);
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
            $user = $this->userProvider->findOneByEmail($oauthEmail);
            if ($user) {
                return $user;
            }

            // No user found, create one using the user info provided by resource server
            $user = $this->um->createUser();
            $user->setEmail($oauthEmail);
            $user->setUsername($oauthEmail);
            $user->setExternalId($oauthId);
            $user->setPassword(uniqid('', true));
            $user->setSuperAdmin(false);
            $user->addRole('ROLE_USER');

            $this->um->updateUser($user);

            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
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
