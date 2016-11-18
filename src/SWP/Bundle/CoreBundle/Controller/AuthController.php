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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Form\Type\UserAuthenticationType;
use SWP\Bundle\CoreBundle\Model\ApiKey;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    /**
     * Look for user matching provided credentials.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Look for user matching provided credentials",
     *     statusCodes={
     *         200="Returned on success.",
     *         401="No user found or not authorized."
     *     }
     * )
     * @Route("/api/{version}/auth", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_auth")
     * @Method("POST")
     */
    public function authenticateAction(Request $request)
    {
        $form = $this->createForm(UserAuthenticationType::class, []);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            $user = $this->get('swp.security.user_provider')->loadUserByUsername($formData['username']);
            if (null !== $user) {
                if ($this->get('security.password_encoder')->isPasswordValid($user, $formData['password'])) {
                    $tokenValidDate = new \DateTime();
                    $tokenValidDate->modify('+48 hours');

                    /* @var ApiKey $apiKey */
                    $apiKey = $this->get('swp.factory.api_key')->create();
                    $apiKey->setApiKey(hash('sha256', random_bytes(52)));
                    $apiKey->setUser($user);
                    $apiKey->setValidTo($tokenValidDate);
                    $this->get('swp.repository.api_key')->add($apiKey);

                    return new SingleResourceResponse([
                        'token' => [
                            'api_key' => $apiKey->getApiKey(),
                            'valid_to' => $apiKey->getValidTo(),
                        ],
                        'user' => $user,
                    ]);
                }
            }
        }

        return new SingleResourceResponse([
            'status' => 401,
            'message' => 'Unauthorized',
        ]);
    }
}
