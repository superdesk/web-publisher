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

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Bundle\UserBundle\Event\FormEvent;
use SWP\Bundle\UserBundle\Event\GetResponseUserEvent;
use SWP\Bundle\UserBundle\Form\Type\ProfileFormType;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\UserBundle\SWPUserEvents;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController
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
     * @var EntityRepository
     */
    private $userRepository;

    public function __construct(
        UserManagerInterface $userManager,
        EventDispatcherInterface $dispatcher,
        EntityRepository $userRepository
    ) {
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/{version}/users/profile/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_get_user_profile")
     */
    public function getAction($id)
    {
        $requestedUser = $this->userRepository->find($id);
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
        $requestedUser = $this->userRepository->find($id);
        if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
            throw $this->createNotFoundException('Requested user don\'t exists');
        }

        $this->checkIfCanAccess($requestedUser);

        $event = new GetResponseUserEvent($requestedUser, $request);
        $this->dispatcher->dispatch($event, SWPUserEvents::PROFILE_EDIT_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('form.factory')
            ->createNamed('', ProfileFormType::class, $requestedUser, ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->dispatcher->dispatch($event, SWPUserEvents::PROFILE_EDIT_SUCCESS);
            $this->userManager->updateUser($requestedUser);

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
