<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Bundle\TemplatesSystemBundle\Form\Type\ContainerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ContainerController extends Controller
{
    /**
     * Lists all registered containers.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered containers",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/templates/containers/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list_containers")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.container');
        $containers = $repository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($containers);
    }

    /**
     * Get single container.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Get single container",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Container not found"
     *     }
     * )
     * @Route("/api/{version}/templates/containers/{uuid}", requirements={"uuid"="\w+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_container")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     *
     * @param string $uuid
     *
     * @return SingleResourceResponse
     */
    public function getAction($uuid)
    {
        $container = $this->get('swp.provider.container')->getOneById($uuid);
        if (!$container) {
            throw new NotFoundHttpException('Container with this uuid was not found.');
        }

        return new SingleResourceResponse($container);
    }

    /**
     * Render single container and it's widgets.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Render single container and it's widgets.",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Container not found"
     *     }
     * )
     * @Route("/api/{version}/templates/containers/{uuid}/render/", requirements={"uuid"="\w+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_render_container")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     */
    public function renderAction($uuid)
    {
        /** @var ContainerInterface $container */
        $container = $this->get('swp.provider.container')->getOneById($uuid);

        if (!$container) {
            throw new NotFoundHttpException('Container with this uuid was not found.');
        }

        $content = $this->get('templating')
            ->render('SWPCoreBundle:Container:render.html.twig', ['containerName' => $container->getName()]);

        return new SingleResourceResponse(['content' => $content]);
    }

    /**
     * Update single container.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single container",
     *     statusCodes={
     *         201="Returned on success.",
     *         404="Container not found",
     *         422="Container id is not number"
     *     },
     *     input="SWP\Bundle\TemplatesSystemBundle\Form\Type\ContainerType"
     * )
     * @Route("/api/{version}/templates/containers/{uuid}", requirements={"uuid"="\w+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_container")
     * @Method("PATCH")
     *
     * @param Request $request
     * @param string  $uuid
     *
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request, $uuid)
    {
        $container = $this->getContainerForUpdate($uuid);
        if (!$container) {
            throw new NotFoundHttpException('Container with this uuid was not found.');
        }

        $form = $this->createForm(ContainerType::class, $container, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $container = $this->get('swp_template_engine.container.service')
                ->updateContainer($container, $form->get('data')->getExtraData());

            return new SingleResourceResponse($container, new ResponseContext(201));
        }

        return new SingleResourceResponse($form);
    }

    /**
     * Link or Unlink resource with Container.
     *
     * **link or unlink widget**:
     *
     *     header name: "link"
     *     header value: "</api/{version}/templates/widgets/{id}; rel="widget">"
     *
     * or with specific position:
     *
     *     header name: "link"
     *     header value: "</api/{version}/templates/widgets/{id}; rel="widget">,<1; rel="widget-position">"
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found",
     *         409={
     *           "Returned when the link already exists",
     *         }
     *     }
     * )
     *
     * @Route("/api/{version}/templates/containers/{uuid}", requirements={"uuid"="\w+"}, defaults={"version"="v1"}, name="swp_api_templates_link_container")
     *
     * @Method("LINK|UNLINK")
     *
     * @param Request $request
     * @param string  $uuid
     *
     * @throws NotFoundHttpException
     * @throws \Exception
     *
     * @return SingleResourceResponse
     */
    public function linkUnlinkToContainerAction(Request $request, $uuid)
    {
        $entityManager = $this->get('swp.object_manager.container');
        $container = $this->getContainerForUpdate($uuid);
        if (!$container) {
            throw new NotFoundHttpException('Container with this uuid was not found.');
        }

        $matched = false;
        foreach ($request->attributes->get('links', []) as $key => $objectArray) {
            if (!is_array($objectArray)) {
                continue;
            }

            $object = $objectArray['object'];
            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof WidgetModelInterface) {
                $container = $this->get('swp_template_engine.container.service')
                    ->linkUnlinkWidget($object, $container, $request);
                $matched = true;
                break;
            }
        }
        if ($matched === false) {
            throw new NotFoundHttpException('Any supported link object was not found');
        }

        $entityManager->flush();

        return new SingleResourceResponse($container, new ResponseContext(201));
    }

    private function getContainerForUpdate($uuid)
    {
        $revisionContext = $this->get('swp_revision.context.revision');
        $currentRenditionBackup = $revisionContext->getCurrentRevision();
        $revisionContext->setCurrentRevision($revisionContext->getWorkingRevision());

        $container = $this->get('swp.provider.container')->getOneById($uuid);

        $revisionContext->setCurrentRevision($currentRenditionBackup);
        if (null === $container) {
            if ($revisionContext->getCurrentRevision() !== $revisionContext->getPublishedRevision()) {
                $revisionContext->setCurrentRevision($revisionContext->getPublishedRevision());
            }
            $container = $this->get('swp.provider.container')->getOneById($uuid);
            $revisionContext->setCurrentRevision($currentRenditionBackup);
        }

        return $container;
    }
}
