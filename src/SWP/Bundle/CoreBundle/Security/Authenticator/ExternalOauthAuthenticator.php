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
        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');

        $oauthUser = $this->getOauthClient()->fetchUserFromToken($credentials);
        // FIXME: Canonicalize email!
        $oauthEmail = $oauthUser->getEmail();
        $oauthId = $oauthUser->getId();

        // Is there an existing user with the same oauth id?
        $user = $userProvider->findOneByExternalId($oauthId);
        if($user) {
            if($user->getEmail() !== $oauthEmail) {
                // The user has the same ID but a new email, meaning the email has
                // been updated on the authentication server. Update it here as well.
                $user->setEmail($oauthEmail);
                $userManager->updateUser($user);
            }
            return $user;
        }

        // Is there an existing user with the same email address?
        $user = $userProvider->findOneByEmail($oauthEmail);
        if($user) {
            return $user;
        }


        // If the user has never logged in before, create the user 
        // using the information provided by OAuth
        $user = $userManager->createUser();
        $user->setEmail($oauthEmail);
        $user->setUsername($oauthId);
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->setExternalId($oauthId);
        $userManager->updateUser($user);

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
