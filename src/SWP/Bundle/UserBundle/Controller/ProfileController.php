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

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\UserBundle\Form\ProfileFormType;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\Repository\UserRepository;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController {

  private EntityManagerInterface $entityManager;
  private AuthorizationCheckerInterface $authorizationChecker;
  private UserRepository $userRepository;

  /**
   * @param EntityManagerInterface $entityManager
   * @param AuthorizationCheckerInterface $authorizationChecker
   * @param UserRepository $userRepository
   */
  public function __construct(EntityManagerInterface        $entityManager,
                              AuthorizationCheckerInterface $authorizationChecker, UserRepository $userRepository) {
    $this->entityManager = $entityManager;
    $this->authorizationChecker = $authorizationChecker;
    $this->userRepository = $userRepository;
  }


  /**
   * @Route("/api/{version}/users/profile/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_get_user_profile")
   */
  public function getAction($id) {
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
  public function editAction(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder) {
    $requestedUser = $this->userRepository->find($id);
    if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
      throw new NotFoundHttpException('Requested user don\'t exists');
    }

    $this->checkIfCanAccess($requestedUser);

    $form = $this->createForm(ProfileFormType::class, $requestedUser, [
        'method' => 'PATCH',
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

      $entityManager = $this->entityManager;
      $entityManager->flush();

      return new SingleResourceResponse($requestedUser);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  private function checkIfCanAccess($requestedUser) {
    /** @var UserInterface $currentUser */
    $currentUser = $this->getUser();
    if (
        !$this->authorizationChecker->isGranted('ROLE_ADMIN') &&
        $requestedUser->getId() !== $currentUser->getId()
    ) {
      throw new AccessDeniedException('This user does not have access to this section. profile');
    }
  }
}
