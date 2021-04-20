<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Controller;

use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\UserBundle\Form\RegistrationFormType;
use SWP\Bundle\UserBundle\Mailer\MailerInterface;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\UserBundle\Security\EmailVerifier;
use SWP\Bundle\UserBundle\Security\LoginAuthenticator;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;
    /**
     * @var ScopeContextInterface
     */
    private $scopeContext;
    /**
     * @var EmailVerifier
     */
    private $emailVerifier;
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(
        SettingsManagerInterface $settingsManager,
        ScopeContextInterface $scopeContext,
        EmailVerifier $emailVerifier,
        UserManagerInterface $userManager
    ) {
        $this->settingsManager = $settingsManager;
        $this->scopeContext = $scopeContext;
        $this->emailVerifier = $emailVerifier;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/api/{version}/users/register/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_register_user")
     */
    public function registerAction(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserManagerInterface $userManager,
        MailerInterface $mailer
    ) {
        try {
            $this->ensureThatRegistrationIsEnabled();
        } catch (NotFoundHttpException $e) {
            return new SingleResourceResponse(null, new ResponseContext(404));
        }

        $user = $userManager->createUser();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addRole('ROLE_USER');
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $this->emailVerifier->getSignatureComponents('swp_user_verify_email', $user);
            $url = $signatureComponents->getSignedUrl();

            $mailer->sendConfirmationEmail($user, $url);

            return new JsonResponse([
                'message' => sprintf(
                    'The user has been created successfully.
                 An email has been sent to %s. It contains an activation link you must click to activate your account.',
                    $user->getEmail()
                ),
                'url' => $url,
            ]);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/verify/email", name="swp_user_verify_email")
     */
    public function verifyUserEmail(Request $request, GuardAuthenticatorHandler $guardHandler, LoginAuthenticator $authenticator): Response
    {
        $id = (int) $request->get('id'); // retrieve the user id from the url

        if ($request->isXmlHttpRequest()) {
            return $this->verifyUserEmailFromPWA($id, $request);
        }

        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('homepage');
        }

        $user = $this->userManager->find($id);

        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->redirectToRoute('homepage');
        }
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('homepage');
        }

        $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main' // firewall name in security.yaml
        );

        $this->addFlash('success', 'The user has been created successfully.');

        return $this->redirectToRoute('swp_user_registration_confirmed');
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
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function ensureThatRegistrationIsEnabled()
    {
        $settingName = 'registration_enabled';
        $setting = $this->settingsManager->getOneSettingByName($settingName);
        $registrationEnabled = $this->settingsManager
            ->get($settingName, $setting['scope'], $this->scopeContext->getScopeOwner($setting['scope']));
        if (!$registrationEnabled) {
            throw new NotFoundHttpException('Registration is disabled.');
        }
    }

    private function verifyUserEmailFromPWA(int $id, Request $request): JsonResponse
    {
        // Verify the user id exists and is not null
        if (null === $id) {
            return new JsonResponse(
                ['error' => 'User does not exist']
            );
        }

        $user = $this->userManager->find($id);

        // Ensure the user exists in persistence
        if (null === $user) {
            return new JsonResponse(
                ['error' => 'User does not exist']
            );
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return new JsonResponse(
                ['error' => 'Registration confirmation invalid']
            );
        }

        return new JsonResponse(
            ['message' => 'The user has been created successfully.']
        );
    }
}
