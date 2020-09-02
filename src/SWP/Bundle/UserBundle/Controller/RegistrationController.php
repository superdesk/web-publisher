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

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\UserBundle\Form\Type\RegistrationFormType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends Controller
{
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

        /** @var UserManagerInterface $userManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('form.factory')->createNamed('', RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $formData */
            $formData = $form->getData();

            if (null !== $userManager->findUserByEmail($formData->getEmail())) {
                throw new ConflictHttpException(sprintf('User with email "%s" already exists', $formData->getEmail()));
            }

            if (null !== $userManager->findUserByUsername($formData->getUsername())) {
                throw new ConflictHttpException(sprintf('User with username "%s" already exists', $formData->getUsername()));
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            $formData->addRole('ROLE_USER');
            $this->get('swp.repository.user')->add($formData);

            if (null === ($response = $event->getResponse())) {
                return new SingleResourceResponse($formData, new ResponseContext(201));
            }
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);
        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @throws NotFoundHttpException
     */
    private function ensureThatRegistrationIsEnabled()
    {
        $settingName = 'registration_enabled';
        $settingsManager = $this->get('swp_settings.manager.settings');
        $scopeContext = $this->get('swp_settings.context.scope');

        $setting = $settingsManager->getOneSettingByName($settingName);
        $registrationEnabled = $settingsManager->get($settingName, $setting['scope'], $scopeContext->getScopeOwner($setting['scope']));
        if (!$registrationEnabled) {
            throw new NotFoundHttpException('Registration is disabled.');
        }
    }
}
