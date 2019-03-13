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
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\CoreBundle\Form\Type\PublishDestinationType;
use SWP\Bundle\CoreBundle\Model\PublishDestinationInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublishDestinationController extends Controller
{
    /**
     * Add a new publish destination.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Add a new publish destination",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\PublishDestinationType"
     * )
     *
     * @Route("/api/{version}/organization/destinations/", options={"expose"=true}, defaults={"version"="v1"}, methods={"POST"}, name="swp_api_core_publishing_destination_create")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(Request $request): SingleResourceResponse
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $destination = $this->get('swp.factory.publish_destination')->create();
        $form = $this->createForm(PublishDestinationType::class, $destination, ['method' => $request->getMethod()]);
        $currentOrganization = $tenantContext->getTenant()->getOrganization();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $repository = $this->get('swp.repository.publish_destination');
            /** @var PublishDestinationInterface $publishDestination */
            $publishDestination = $repository->findOneByTenant($destination->getTenant());
            if (null !== $publishDestination) {
                $repository->remove($publishDestination);
            }

            $currentOrganization->addPublishDestination($destination);
            $this->get('swp.object_manager.publish_destination')->flush();

            return new SingleResourceResponse($destination, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Updates existing publish destination.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update existing publish destination",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\PublishDestinationType"
     * )
     *
     * @Route("/api/{version}/organization/destinations/{id}", options={"expose"=true}, defaults={"version"="v1"}, methods={"PATCH"}, name="swp_api_core_publishing_destination_update", requirements={"id"="\d+"})
     * @ParamConverter("publishDestination", class="SWP\Bundle\CoreBundle\Model\PublishDestination")
     */
    public function updateAction(Request $request, PublishDestinationInterface $publishDestination): SingleResourceResponse
    {
        $objectManager = $this->get('swp.object_manager.publish_destination');

        $form = $this->createForm(PublishDestinationType::class, $publishDestination, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($publishDestination);

            return new SingleResourceResponse($publishDestination);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
