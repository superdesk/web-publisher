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
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FbiaArticleController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Lists Facebook Instant Articles submitted articles",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Unexpected error."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/facebook/instantarticles/articles/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_list_facebook_instant_articles_articles")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.facebook_instant_articles_article');

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
     *     description="Updates status of submitted Instant Article",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Unexpected error."
     *     }
     * )
     * @Route("/api/{version}/facebook/instantarticles/articles/{submissionId}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_facebook_instant_articles_articles_update")
     * @Method("POST")
     */
    public function updateSubmissionAction(string $submissionId)
    {
        $instantArticlesService = $this->get('swp.facebook.service.instant_articles');
        $instantArticle = $instantArticlesService->updateSubmissionStatus($submissionId);

        return new SingleResourceResponse($instantArticle);
    }
}
