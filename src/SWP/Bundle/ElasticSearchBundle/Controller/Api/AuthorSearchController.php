<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher ElasticSearch Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ElasticSearchBundle\Controller\Api;

use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorSearchController extends Controller
{
    /**
     * @Route("/api/{version}/authors/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_authors")
     */
    public function searchAction(Request $request)
    {
        $criteria = Criteria::fromQueryParameters(
            $request->query->get('term', ''),
            [
                'page' => $request->query->get('page'),
                'sort' => $request->query->get('sorting'),
                'limit' => $request->query->get('limit', 10),
            ]
        );

        $result = $this->get('fos_elastica.manager')
            ->getRepository($this->getParameter('swp.model.author.class'))
            ->findByCriteria($criteria);

        $pagination = $this->get('knp_paginator')->paginate(
            $result,
            $request->query->get('page', 1),
            $criteria->getPagination()->getItemsPerPage()
        );

        $responseContext = new ResponseContext();
        $responseContext->setSerializationGroups(
            [
                'Default',
                'api_packages_list',
                'api_packages_items_list',
                'api_tenant_list',
                'api_articles_list',
                'api_articles_slideshows',
                'api_articles_featuremedia',
                'api_articles_statistics_list',
                'api_article_authors',
                'api_article_media_list',
                'api_article_media_renditions',
                'api_image_details',
                'api_groups_list',
                'api_routes_list',
            ]
        );

        return new ResourcesListResponse($pagination, $responseContext);
    }
}
