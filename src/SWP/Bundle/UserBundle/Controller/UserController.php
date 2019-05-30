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

use FOS\UserBundle\Model\UserManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Bundle\UserBundle\Form\Type\UserRolesType;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{
    /**
     * @Operation(
     *     tags={"user"},
     *     summary="Change user roles",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=UserRolesType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\User::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned on user not found."
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when user don't have permissions to change roles"
     *     )
     * )
     *
     *
     * @Route("/api/{version}/users/{id}/promote", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_promote_user")
     * @Route("/api/{version}/users/{id}/demote", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_user_demote_user")
     */
    public function modifyRolesAction(Request $request, $id)
    {
        $requestedUser = $this->container->get('swp.repository.user')->find($id);
        if (!is_object($requestedUser) || !$requestedUser instanceof UserInterface) {
            throw new NotFoundHttpException('Requested user don\'t exists');
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->get('form.factory')->createNamed('', UserRolesType::class, [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
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
}
