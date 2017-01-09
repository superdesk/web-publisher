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

use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Pagination\PaginationInterface;
use SWP\Bundle\TemplatesSystemBundle\Form\Type\ContainerType;
use SWP\Bundle\TemplatesSystemBundle\Model\ContainerWidget;
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
     *     }
     * )
     * @Route("/api/{version}/templates/containers/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list_containers")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $containerProvider = $this->get('swp.provider.container');
        $paginator = $this->get('knp_paginator');
        $containers = $paginator->paginate(
            $containerProvider->getQueryForAll(),
            $request->get(PaginationInterface::PAGE_PARAMETER_NAME, 1),
            $request->get(PaginationInterface::LIMIT_PARAMETER_NAME, 10)
        );

        if (count($containers) == 0) {
            throw new NotFoundHttpException('Containers were not found.');
        }

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
     *         404="Container not found",
     *         422="Container id is not number"
     *     }
     * )
     * @Route("/api/{version}/templates/containers/{uuid}", requirements={"uuid"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_container")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($uuid)
    {
        if (!$uuid) {
            throw new UnprocessableEntityHttpException('You need to provide container Uuid (string).');
        }

        $container = $this->get('swp.provider.container')->getOneById($uuid);
        if (!$container) {
            throw new NotFoundHttpException('Container with this uuid was not found.');
        }

        return new SingleResourceResponse($container);
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
     * @Route("/api/{version}/templates/containers/{uuid}", requirements={"uuid"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_container")
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
        if (!$uuid) {
            throw new UnprocessableEntityHttpException('You need to provide container Uuid (string).');
        }

        $container = $this->get('swp.provider.container')->getOneById($uuid);
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
     *     header value: "</api/{version}/templates/widgets/{uuid}; rel="widget">"
     *
     * or with specific position:
     *
     *     header name: "link"
     *     header value: "</api/{version}/templates/widgets/{uuid}; rel="widget">,<1; rel="widget-position">"
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
     * @Route("/api/{version}/templates/containers/{uuid}", requirements={"id"="\d+"}, defaults={"version"="v1"}, name="swp_api_templates_link_container")
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
        if (!$uuid) {
            throw new UnprocessableEntityHttpException('You need to provide container Uuid (string).');
        }

        $entityManager = $this->get('doctrine')->getManager();
        $container = $this->get('swp.provider.container')->getOneById($uuid);
        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
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
                $containerWidget = $entityManager->getRepository('SWP\Bundle\TemplatesSystemBundle\Model\ContainerWidget')
                    ->findOneBy([
                        'widget' => $object,
                        'container' => $container,
                    ]);
                if ($request->getMethod() === 'LINK') {
                    $position = false;
                    if (count($notConvertedLinks = $this->getNotConvertedLinks($request)) > 0) {
                        foreach ($notConvertedLinks as $link) {
                            if (isset($link['resourceType']) && $link['resourceType'] == 'widget-position') {
                                $position = $link['resource'];
                            }
                        }
                    }

                    if ($position === false && $containerWidget) {
                        throw new \Exception('WidgetModel is already linked to container', 409);
                    }

                    if (!$containerWidget) {
                        $containerWidget = new ContainerWidget($container, $object);
                        $entityManager->persist($containerWidget);
                    }

                    if ($position !== false) {
                        $containerWidget->setPosition($position);
                        $entityManager->persist($containerWidget);
                        $container->addWidget($containerWidget);
                        $entityManager->flush();
                    }
                } elseif ($request->getMethod() === 'UNLINK') {
                    if (!$container->getWidgets()->contains($containerWidget)) {
                        throw new \Exception('WidgetModel is not linked to container', 409);
                    }
                    $entityManager->remove($containerWidget);
                }

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

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getNotConvertedLinks($request)
    {
        $links = [];
        foreach ($request->attributes->get('links') as $idx => $link) {
            if (is_string($link)) {
                $linkParams = explode(';', trim($link));
                $resourceType = null;
                if (count($linkParams) > 1) {
                    $resourceType = trim(preg_replace('/<|>/', '', $linkParams[1]));
                    $resourceType = str_replace('"', '', str_replace('rel=', '', $resourceType));
                }
                $resource = array_shift($linkParams);
                $resource = preg_replace('/<|>/', '', $resource);

                $links[] = [
                    'resource' => $resource,
                    'resourceType' => $resourceType,
                ];
            }
        }

        return $links;
    }
}
