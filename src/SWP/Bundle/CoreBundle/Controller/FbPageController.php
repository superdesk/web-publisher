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
use SWP\Bundle\CoreBundle\Form\Type\FacebookPageType;
use SWP\Bundle\CoreBundle\Model\FacebookPage;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FbPageController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Lists Facebook Pages",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Unexpected error."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/facebook/pages/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_list_facebook_pages")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.facebook_page');

        $items = $repository->getPaginatedByCriteria(
            new Criteria(),
            $request->query->get('sorting', ['id' => 'asc']),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Create Facebook page",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when not valid data."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\FacebookPageType"
     * )
     * @Route("/api/{version}/facebook/pages/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_create_facebook_pages")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        /* @var FacebookPage $feed */
        $page = $this->get('swp.factory.facebook_page')->create();
        $form = $this->createForm(FacebookPageType::class, $page, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->checkIfPageExists($page);
            $this->get('swp.repository.facebook_page')->add($page);

            return new SingleResourceResponse($page, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Delete Facebook page",
     *     statusCodes={
     *         204="Returned on success.",
     *         500="Unexpected error.",
     *         404="Page not found",
     *         409="Page is used by Instant Articles Feed"
     *     }
     * )
     * @Route("/api/{version}/facebook/pages/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_delete_facebook_pages")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $repository = $this->get('swp.repository.facebook_page');
        if (null === $page = $this->get('swp.repository.facebook_page')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('There is no Page with provided id!');
        }

        if (null !== $feed = $this->get('swp.repository.facebook_instant_articles_feed')->findOneBy(['facebookPage' => $id])) {
            throw new ConflictHttpException(sprintf('This Page is used by Instant Articles Feed with id: %s!', $feed->getId()));
        }

        $repository->remove($page);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @param PageInterface $page
     */
    private function checkIfPageExists(PageInterface $page)
    {
        if (null !== $this->get('swp.repository.facebook_page')->findOneBy([
                'pageId' => $page->getPageId(),
            ])
        ) {
            throw new ConflictHttpException('This Page already exists!');
        }
    }
}
