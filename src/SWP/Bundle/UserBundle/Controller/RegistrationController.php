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

use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\UserBundle\Form\RegistrationFormType;
use SWP\Bundle\UserBundle\Mailer\MailerInterface;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelper;

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

    public function __construct(
        SettingsManagerInterface $settingsManager,
        ScopeContextInterface $scopeContext
    ) {
        $this->settingsManager = $settingsManager;
        $this->scopeContext = $scopeContext;
    }

    /**
     * @Route("/api/{version}/users/register/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_register_user")
     */
    public function registerAction(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserManagerInterface $userManager,
        RouterInterface $router,
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
            $user->setEnabled(false);
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
            $user->setConfirmationToken($token);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $url = $router->generate(
                'swp_user_registration_confirm',
                ['token' => $user->getConfirmationToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $mailer->sendConfirmationEmail($user, $url);

            return new JsonResponse([
                'message' => sprintf(
                    'The user has been created successfully.
                 An email has been sent to %s. It contains an activation link you must click to activate your account.',
                    $user->getEmail()
                ),
                'url' => $url
            ]);
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
    public function confirmAction(Request $request, $token, UserManagerInterface $userManager)
    {

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')
                ->generate('swp_user_security_login'));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $url = $this->generateUrl('swp_user_registration_confirmed');
        $response = new RedirectResponse($url);

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
}
