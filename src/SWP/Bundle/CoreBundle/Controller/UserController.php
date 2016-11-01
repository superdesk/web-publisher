<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Form\Type\RegistrationFormType;
use SWP\Bundle\CoreBundle\Response\ResponseContext;
use SWP\Bundle\CoreBundle\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UserController extends FOSRestController
{
    /**
     * Register new user.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Register new user",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on failure.",
     *         409="Returned on conflict."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\RegistrationFormType"
     * )
     * @Route("/api/{version}/users/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_register_user")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var UserInterface $formData */
            $formData = $form->getData();

            if (null !== $userManager->findUserByEmail($formData->getEmail())) {
                throw new ConflictHttpException(sprintf('User with email "%s" already exists', $formData->getEmail()));
            }

            if (null !== $userManager->findUserByUsername($formData->getUsername())) {
                throw new ConflictHttpException(sprintf('User with username "%s" already exists', $formData->getUsername()));
            }

            $this->get('swp.repository.user')->add($formData);

            return new SingleResourceResponse($formData, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
