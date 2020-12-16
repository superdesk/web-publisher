<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Controller;

use SWP\Bundle\SettingsBundle\Context\AbstractScopeContext;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Bundle\UserBundle\Event\FilterUserResponseEvent;
use SWP\Bundle\UserBundle\Event\FormEvent;
use SWP\Bundle\UserBundle\Event\GetResponseUserEvent;
use SWP\Bundle\UserBundle\Form\Type\RegistrationFormType;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\UserBundle\SWPUserEvents;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;
    /**
     * @var AbstractScopeContext
     */
    private $scopeContext;
    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(UserManagerInterface $userManager,
                                EventDispatcherInterface $dispatcher,
                                SettingsManagerInterface $settingsManager,
                                ScopeContextInterface $scopeContext,
                                EntityRepository $userRepository,
                                TokenStorageInterface $tokenStorage
    ) {
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->settingsManager = $settingsManager;
        $this->scopeContext = $scopeContext;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/{version}/users/register/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_register_user")
     */
    public function registerAction(Request $request)
    {
        try {
            $this->ensureThatRegistrationIsEnabled();
        } catch (NotFoundHttpException $e) {
            return new SingleResourceResponse(null, new ResponseContext(404));
        }

        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->dispatcher->dispatch($event, SWPUserEvents::REGISTRATION_INITIALIZE);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $formData */
            $formData = $form->getData();

            if (null !== $this->userManager->findUserByEmail($formData->getEmail())) {
                throw new ConflictHttpException(sprintf('User with email "%s" already exists', $formData->getEmail()));
            }

            if (null !== $this->userManager->findUserByUsername($formData->getUsername())) {
                throw new ConflictHttpException(sprintf('User with username "%s" already exists',
                    $formData->getUsername()));
            }

            $event = new FormEvent($form, $request);
            $this->dispatcher->dispatch($event, SWPUserEvents::REGISTRATION_SUCCESS);
            $formData->addRole('ROLE_USER');

            $this->userRepository->add($formData);

            if (null === ($response = $event->getResponse())) {
                return new SingleResourceResponse($formData, new ResponseContext(201));
            }
            $this->dispatcher->dispatch(new FilterUserResponseEvent($user, $request, $response),
                SWPUserEvents::REGISTRATION_COMPLETED);

            return $response;
        }

        $event = new FormEvent($form, $request);
        $this->dispatcher->dispatch($event, SWPUserEvents::REGISTRATION_FAILURE);
        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param string $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('swp_user_security_login'));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->dispatcher->dispatch($event, SWPUserEvents::REGISTRATION_CONFIRM);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('swp_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $this->dispatcher->dispatch(new FilterUserResponseEvent($user, $request, $response), SWPUserEvents::REGISTRATION_CONFIRMED);

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $this->createAccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@SWPUser/Registration/confirmed.html.twig', [
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function ensureThatRegistrationIsEnabled()
    {
        $settingName = 'registration_enabled';
        $setting = $this->settingsManager->getOneSettingByName($settingName);
        $registrationEnabled = $this->settingsManager->get($settingName, $setting['scope'], $this->scopeContext->getScopeOwner($setting['scope']));
        if (!$registrationEnabled) {
            throw new NotFoundHttpException('Registration is disabled.');
        }
    }

    /**
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session)
    {
        if ($this->getUser()) {
            $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

            if ($session->has($key)) {
                return $session->get($key);
            }
        }

        return null;
    }

    /**
     * Tell the user to check their email provider.
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->getSession()->get('swp_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->generateUrl('swp_user_registration_register'));
        }

        $request->getSession()->remove('swp_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('swp_user_security_login'));
        }

        return $this->render('@SWPUser/Registration/check_email.html.twig', [
            'user' => $user,
        ]);
    }
}
