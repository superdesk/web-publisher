<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListType;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Event\HttpCacheEvent;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\ContentList\Model\ContentListInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentListController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all content lists",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/lists/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_lists")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.content_list');

        $lists = $repository->getPaginatedByCriteria(new Criteria(), [], new PaginationData($request));

        return $this->handleView(View::create($this->get('swp_pagination_rep')->createRepresentation($lists, $request), 200));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Show single content list",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_show_lists", requirements={"id"=".+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($id)
    {
        return $this->handleView(View::create($this->findOr404($id), 200));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Create new content list",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when not valid data."
     *     },
     *     input="SWP\Bundle\ContentListBundle\Form\Type\ContentListType"
     * )
     * @Route("/api/{version}/content/lists/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_create_lists")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        /* @var ContentListInterface $route */
        $contentList = $this->get('swp.factory.content_list')->create();
        $form = $this->createForm(ContentListType::class, $contentList, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->ensureContentListExists($contentList->getName());

        if ($form->isValid()) {
            $this->get('swp.repository.content_list')->add($contentList);
            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($contentList));

            return $this->handleView(View::create($contentList, 201));
        }

        return $this->handleView(View::create($form, 400));
    }

    private function findOr404($id)
    {
        if (null === $list = $this->get('swp.repository.content_list')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Content list with id "%s" was not found.', $id));
        }

        return $list;
    }

    private function ensureContentListExists($name)
    {
        if (null !== $this->get('swp.repository.content_list')->findOneByName($name)) {
            throw new ConflictHttpException(sprintf('Content list named "%s" already exists!', $name));
        }
    }
}
