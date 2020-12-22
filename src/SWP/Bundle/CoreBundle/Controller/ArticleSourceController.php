<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleSourceController extends Controller
{
    /**
     * @Route("/api/{version}/content/sources/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_article_sources")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $repository = $this->get('swp.repository.article_source');

        $lists = $repository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($lists);
    }
}
