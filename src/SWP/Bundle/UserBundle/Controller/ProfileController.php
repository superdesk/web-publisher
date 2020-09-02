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

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\UserBundle\Form\Type\ProfileFormType;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends Controller
{
    /**
     * @Route("/api/{version}/users/profile/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_get_user_profile")
     */
    public function getAction($id)
    {
        $requestedUser = $this->container->get('swp.repository.user')->find($id);
        if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
            throw new NotFoundHttpException('Requested user don\'t exists');
        }

        $this->checkIfCanAccess($requestedUser);

        return new SingleResourceResponse($requestedUser);
    }

    /**
     * @Route("/api/{version}/users/profile/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_edit_user_profile")
     */
    public function editAction(Request $request, $id)
    {
        $requestedUser = $this->container->get('swp.repository.user')->find($id);
        if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
            throw new NotFoundHttpException('Requested user don\'t exists');
        }

        $this->checkIfCanAccess($requestedUser);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $event = new GetResponseUserEvent($requestedUser, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('form.factory')->createNamed('', ProfileFormType::class, $requestedUser, ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
            $userManager->updateUser($requestedUser);

            return new SingleResourceResponse($requestedUser);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function checkIfCanAccess($requestedUser)
    {
        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();
        if (
            !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            $requestedUser->getId() !== $currentUser->getId()
        ) {
            throw new AccessDeniedException('This user does not have access to this section. profile');
        }
    }
}
