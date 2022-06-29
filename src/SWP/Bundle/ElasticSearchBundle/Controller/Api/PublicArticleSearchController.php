<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher ElasticSearch Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ElasticSearchBundle\Controller\Api;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class PublicArticleSearchController extends ArticleSearchController
{
    /**
     * @Route("/api/{version}/search/articles/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_public_api_content_list_articles")
     */
    public function searchAction(Request $request, RepositoryManagerInterface $repositoryManager)
    {
        return parent::searchAction($request, $repositoryManager);
    }

    protected function getSerializationGroups(): array
    {
        return [
            'Default',
            'public_api',
        ];
    }

    protected function createAdditionalCriteria(Request $request): array
    {
        return [
            'statuses' => [ArticleInterface::STATUS_PUBLISHED],
        ];
    }
}
