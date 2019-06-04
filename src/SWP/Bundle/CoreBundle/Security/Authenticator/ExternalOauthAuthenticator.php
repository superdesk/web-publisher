<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Security\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface as CoreUserInterface;
use SWP\Bundle\CoreBundle\Security\Provider\UserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ExternalOauthAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
    }

    public function supports(Request $request) 
    {
        return $request->headers->has('Authorization') && 
                preg_match('/^Bearer/', $request->headers['Authorization']);
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getOauthClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $oauthUser = $this->getOauthClient()->fetchUserFromToken($credentials);
        $oauthEmail = $oauthUser->getEmail();
        $oauthId = $oauthUser->getId();

        // Is there an existing user with the same email address?
        $existingUser = $userProvider->findOneByEmail($oauthEmail);
        if($existingUser) {
            return $existingUser;
        }

        // Create a new user
        $newUser = $this->em->getRepository(User::class);
    }

    private function getOauthClient()
    {
        return $this->clientRegistry->getClient('external_oauth');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

}
