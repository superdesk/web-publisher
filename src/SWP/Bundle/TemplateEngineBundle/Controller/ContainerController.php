<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stringy\StaticStringy;
use SWP\Bundle\TemplateEngineBundle\Form\Type\ContainerType;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerData;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerWidget;
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ContainerController extends FOSRestController
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
        $entityManager = $this->get('doctrine')->getManager();
        $paginator = $this->get('knp_paginator');
        $containers = $paginator->paginate($entityManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Container')->getAll());

        if (count($containers) == 0) {
            throw new NotFoundHttpException('Containers were not found.');
        }

        return $this->handleView(View::create($this->container->get('swp_pagination_rep')->createRepresentation($containers, $request), 200));
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
     * @Route("/api/{version}/templates/containers/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_container")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction(Request $request, $id)
    {
        $container = $this->get('doctrine')->getManager()
            ->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Container')
            ->getById($id)
            ->getOneOrNullResult();

        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
        }

        return $this->handleView(View::create($container, 200));
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
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\ContainerType"
     * )
     * @Route("/api/{version}/templates/containers/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_container")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $container = $entityManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Container')
            ->getById($id)
            ->getOneOrNullResult();

        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
        }

        $form = $this->createForm(new ContainerType(), $container, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $extraData = $form->get('data')->getExtraData();
            if ($extraData && is_array($extraData)) {
                // Remove old containerData's
                foreach ($container->getData() as $containerData) {
                    $entityManager->remove($containerData);
                }

                // Apply new containerData's
                foreach ($extraData as $key => $value) {
                    $containerData = new ContainerData($key, $value);
                    $containerData->setContainer($container);
                    $entityManager->persist($containerData);
                    $container->addData($containerData);
                }
            }

            $entityManager->flush($container);
            $entityManager->refresh($container);
            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($container));

            return $this->handleView(View::create($container, 201));
        }

        return $this->handleView(View::create($form, 200));
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
     * @Route("/api/{version}/templates/containers/{id}", requirements={"id"="\d+"}, defaults={"version"="v1"}, name="swp_api_templates_link_container")
     *
     * @Method("LINK|UNLINK")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkUnlinkToContainerAction(Request $request, $id)
    {
        if (!$id) {
            throw new UnprocessableEntityHttpException('You need to provide container Id (integer).');
        }

        $entityManager = $this->get('doctrine')->getManager();
        $container = $entityManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Container')
            ->getById($id)
            ->getOneOrNullResult();

        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
        }

        $matched = false;
        foreach ($request->attributes->get('links', []) as $key => $objectArray) {
            if (!is_array($objectArray)) {
                continue;
            }

            $resourceType = $objectArray['resourceType'];
            $object = $objectArray['object'];

            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof WidgetModel) {
                $containerWidget = $entityManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\ContainerWidget')
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
                        $entityManager->flush($containerWidget);
                    }
                } elseif ($request->getMethod() === 'UNLINK') {
                    if (!$container->getWidgets()->contains($containerWidget)) {
                        throw new \Exception('WidgetModel is not linked to container', 409);
                    }
                    $entityManager->remove($containerWidget);
                }

                $entityManager->flush();
                $this->get('event_dispatcher')
                    ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($container));
                $matched = true;
                break;
            }
        }
        if ($matched === false) {
            throw new NotFoundHttpException('Any supported link object was not found');
        }

        return $this->handleView(View::create($container, 201));
    }

    /**
     * @param Request $request
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
