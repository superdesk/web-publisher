<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Paywall Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\PaywallBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends Controller
{
    /**
     * Lists current user subscriptions.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists current user subscriptions",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     filters={
     *         {"name"="type", "dataType"="string", "pattern"="collection|content"},
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/user/subscriptions/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_paywall_list_user_subscriptions")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $subscriptionRepository = $this->get('swp.repository.subscription');

        /** @var UserInterface $currentUser */
        $currentUser = $this->getUser();

        $subscriptions = $subscriptionRepository->getPaginatedByCriteria(new Criteria([
            'type' => $request->query->get('type', ''),
            'user' => $currentUser->getId(),
        ]), $request->query->get('sorting', []), new PaginationData($request));

        return $this->handleView(View::create($this->get('swp_pagination_rep')->createRepresentation($subscriptions, $request), 200));
    }
}
