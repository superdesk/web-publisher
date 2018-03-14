<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Form\Type\OrganizationPublishDestinationType;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublishDestinationController extends Controller
{
    /**
     * Update publish destinations of the current organization.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update publish destinations of the current organization",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\OrganizationPublishDestinationType"
     * )
     *
     * @Route("/api/{version}/organization/destinations/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_publishing_destination_create")
     *
     * @Method("PUT")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function putAction(Request $request)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $currentOrganization = $tenantContext->getTenant()->getOrganization();
        $form = $this->createForm(OrganizationPublishDestinationType::class, $currentOrganization, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('swp.object_manager.publish_destination')->flush();

            return new SingleResourceResponse(null, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
