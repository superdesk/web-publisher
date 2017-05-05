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
use SWP\Bundle\CoreBundle\Form\Type\FacebookInstantArticlesFeedType;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FbiaFeedController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Lists Facebook Instant Articles feeds",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Unexpected error."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_list_facebook_instant_articles_feed")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.facebook_instant_articles_feed');

        $items = $repository->getPaginatedByCriteria(
            new Criteria(),
            $request->query->get('sorting', ['createdAt' => 'desc']),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Create Facebook Instant Articles feed content list",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when not valid data."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\FacebookInstantArticlesFeedType"
     * )
     * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_create_facebook_instant_articles_feed")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        /* @var FacebookInstantArticlesFeedInterface $feed */
        $feed = $this->get('swp.factory.facebook_instant_articles_feed')->create();
        $form = $this->createForm(FacebookInstantArticlesFeedType::class, $feed, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->checkIfFeedExists($feed->getContentBucket(), $feed->getFacebookPage());
            $this->get('swp.repository.facebook_instant_articles_feed')->add($feed);

            return new SingleResourceResponse($feed, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function checkIfFeedExists($contentBucket, $facebookPage)
    {
        if (null !== $this->get('swp.repository.facebook_instant_articles_feed')->findOneBy([
                'contentBucket' => $contentBucket,
                'facebookPage' => $facebookPage,
            ])) {
            throw new ConflictHttpException('Feed for that page and content bucket already exists!');
        }
    }
}
