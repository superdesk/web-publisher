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
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Security;

class ExternalOauthAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;

    public function __construct(
        ClientRegistry $clientRegistry,
        UserManagerInterface $um,
        Security $security
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->um = $um;
        $this->security = $security;
    }

    public function supports(Request $request) 
    {
        if(!$this->security->getUser() || ($request->query->get('code') && $request->get('state'))) {
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request)
    {
        $authHeader = $request->headers->get('Authorization');
        if($authHeader && preg_match('/^Bearer/', $authHeader)) {
            return array(
                "token" => substr($authHeader, 7)
            );
        } else {
            return $this->fetchAccessToken($this->getOauthClient());
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $oauthUser = $this->getOauthClient()->fetchUserFromToken($credentials);
        // FIXME: Canonicalize email!
        $oauthEmail = $oauthUser->getEmail();
        $oauthId = $oauthUser->getId();

        if(!$oauthUser) {
            return null;
        }

        // Is there an existing user with the same oauth id?
        $user = $userProvider->findOneByExternalId($oauthId);
        if($user) {
            return $user;
        }

        // Is there an existing user with the same email address?
        $user = $userProvider->findOneByEmail($oauthEmail);
        if($user) {
            return $user;
        }

        // If the user has never logged in before, create the user 
        // using the information provided by OAuth
        $user = $this->um->createUser();
        $user->setEmail($oauthUser->getEmail());
        $user->setUsername($oauthUser->getEmail());
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->setExternalId($oauthUser->getId());
        // password is a non-null field, so we'll have to generate a random password
        $user->setPassword(\uniqid());
        // Persist the updated user
        $this->um->updateUser($user);

        return $user;

        // FIXME: Do we need to dispatch a USER_CREATED event? What does that do?
        /** @var $dispatcher EventDispatcherInterface */
        //$dispatcher = $this->get('event_dispatcher');
        // $event = new UserEvent($user, $this->getRequest());
        // $this->dispatcher->dispatch(FOSUserEvents::USER_CREATED, $event);
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
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/oauth/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
