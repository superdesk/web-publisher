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
    /**
     * @var ClientRegistry
     */
    protected $clientRegistry;

    /**
     * @var UserMangerInterface
     */
    protected $em;

    /**
     * @var UserMangerInterface
     */
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

    /**
     * @inehritdoc
     */
    public function supports(Request $request) 
    {
        if(!$this->security->getUser() || ($request->query->get('code') && $request->get('state'))) {
            return true;
        }

        return false;
    }

    /**
     * @inehritdoc
     */
    public function getCredentials(Request $request)
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
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Fetch the user from the resource server
        $oauthUser = $this->getOauthClient()->fetchUserFromToken($credentials);
        $oauthEmail = $oauthUser->getEmail();
        $oauthId = $oauthUser->getId();

        if(!$oauthUser) {
            return null;
        }

        // Is there an existing user with the same oauth id?
        $user = $userProvider->findOneByExternalId($oauthId);
        if($user) {
            if($user->getEmail() !== $oauthEmail) {
                // If the email has changed for the user, update it here as well
                $user->setEmail($oauthEmail);
                $user->setUsername($oauthEmail);
                $this->um->updateUser($user);
            }

            return $user;
        }

        // Is there an existing user with the same email address?
        $user = $userProvider->findOneByEmail($oauthEmail);
        if($user) {
            return $user;
        }

        // No user found, create one using the user info provided by resource server
        $user = $this->um->createUser();
        $user->setEmail($oauthUser->getEmail());
        $user->setUsername($oauthUser->getEmail());
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        // Persist the new user
        $this->um->updateUser($user);

        return $user;

        // FIXME: Do we need to dispatch a USER_CREATED event? What does that do?
        /** @var $dispatcher EventDispatcherInterface */
        //$dispatcher = $this->get('event_dispatcher');
        // $event = new UserEvent($user, $this->getRequest());
        // $this->dispatcher->dispatch(FOSUserEvents::USER_CREATED, $event);
    }

    /**
     * @inehritdoc
     */
    private function getOauthClient()
    {
        return $this->clientRegistry->getClient('external_oauth');
    }

    /**
     * @inehritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    /**
     * @inehritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inehritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/oauth/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
