<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\CoreBundle\Provider\CachedSubscriptionsProvider;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends Controller
{
    /**
     * Lists user subscriptions.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists user subscriptions",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     filters={
     *         {"name"="routeId", "dataType"="integer", "pattern"="\d+"},
     *         {"name"="articleId", "dataType"="integer", "pattern"="\d+"},
     *     }
     * )
     * @Route("/api/{version}/subscriptions/{userId}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_paywall_list_subscriptions", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function getAction(Request $request, int $userId)
    {
        $subscriptionsProvider = $this->get(CachedSubscriptionsProvider::class);

        $filters = [
            'routeId' => $request->query->get('routeId'),
            'articleId' => $request->query->get('articleId'),
        ];

        $user = $this->findOr404($userId);

        $subscriptions = $subscriptionsProvider->getSubscriptions($user, $filters);

        return new SingleResourceResponse($subscriptions);
    }

    private function findOr404(int $id)
    {
        $user = $this->get('swp.repository.user')->findOneById($id);

        if (!$user instanceof UserInterface) {
            throw new NotFoundHttpException(sprintf('User with id "%s" was not found.', $id));
        }

        return $user;
    }
}
