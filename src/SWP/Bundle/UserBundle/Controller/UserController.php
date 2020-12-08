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

use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Form\Type\SettingType;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsInterface;
use SWP\Bundle\UserBundle\Form\Type\UserRolesType;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends AbstractController
{
    protected $settingsManager;

    protected $scopeContext;

    protected $formFactory;

    protected $userRepository;

    public function __construct(
        SettingsManagerInterface $settingsManager,
        ScopeContextInterface $scopeContext,
        FormFactoryInterface $formFactory,
        RepositoryInterface $userRepository
    ) {
        $this->settingsManager = $settingsManager;
        $this->scopeContext = $scopeContext;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/{version}/users/{id}/promote", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_promote_user")
     * @Route("/api/{version}/users/{id}/demote", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_demote_user")
     */
    public function modifyRoles(Request $request, $id, UserManagerInterface $userManager, AuthorizationCheckerInterface $authorizationChecker)
    {
        $requestedUser = $this->userRepository->find($id);
        if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
            throw new NotFoundHttpException('Requested user don\'t exists');
        }

        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->formFactory->createNamed('', UserRolesType::class, [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var $userManager UserManagerInterface */
            foreach (explode(',', $form->getData()['roles']) as $role) {
                $role = trim($role);
                if ('swp_api_user_promote_user' === $request->attributes->get('_route')) {
                    $requestedUser->addRole($role);
                } elseif ('swp_api_user_demote_user' === $request->attributes->get('_route')) {
                    $requestedUser->removeRole($role);
                }
            }
            $userManager->updateUser($requestedUser);

            return new SingleResourceResponse($requestedUser);
        }

        return new SingleResourceResponse($form);
    }

    /**
     * @Route("/api/{version}/users/settings/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_get_settings")
     */
    public function listSettings(): SingleResourceResponseInterface
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return new SingleResourceResponse([
                'status' => 401,
                'message' => 'Unauthorized',
            ], new ResponseContext(401));
        }

        $settings = $this->settingsManager->getByScopeAndOwner(ScopeContextInterface::SCOPE_USER, $user);

        return new SingleResourceResponse($settings);
    }

    /**
     * @Route("/api/{version}/users/settings/", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_update_settings")
     */
    public function updateSettings(Request $request): SingleResourceResponseInterface
    {
        $form = $this->formFactory->createNamed('', SettingType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var SettingsInterface $setting */
            $setting = $this->settingsManager->getOneSettingByName($data['name']);
            $scope = $setting['scope'];

            if (null === $setting || ScopeContextInterface::SCOPE_USER !== $scope) {
                throw new NotFoundHttpException('Setting with this name was not found.');
            }

            $owner = null;
            if (ScopeContextInterface::SCOPE_GLOBAL !== $scope) {
                $owner = $this->scopeContext->getScopeOwner($scope);
                if (null === $owner) {
                    throw new InvalidScopeException($scope);
                }
            }

            $setting = $this->settingsManager->set($data['name'], $data['value'], $scope, $owner);

            return new SingleResourceResponse($setting);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
