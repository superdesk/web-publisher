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

use SWP\Bundle\UserBundle\Form\ProfileFormType;
use SWP\Bundle\UserBundle\Repository\UserRepository;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SWP\Bundle\UserBundle\Model\UserInterface;

class ProfileController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {

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
    public function editAction(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder)
    {
        $requestedUser = $this->userRepository->find($id);
        if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
            throw new NotFoundHttpException('Requested user don\'t exists');
        }

        $this->checkIfCanAccess($requestedUser);

        $form = $this->createForm(ProfileFormType::class, $requestedUser, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($form->get('plainPassword')->getData())) {
                $requestedUser->setPassword(
                    $passwordEncoder->encodePassword(
                        $requestedUser,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

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
