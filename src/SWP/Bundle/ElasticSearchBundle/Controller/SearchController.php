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

namespace SWP\Bundle\ElasticSearchBundle\Controller;

use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Repository\ArticleRepository;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller
{
    public function searchAction(string $template = '', array $criteria = [])
    {
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();

        $criteria['tenantCode'] = $currentTenant->getCode();
        $criteria = Criteria::fromQueryParameters($criteria['term'], $criteria);
        $repositoryManager = $this->get('fos_elastica.manager');
        /** @var ArticleRepository $repository */
        $repository = $repositoryManager->getRepository($this->getParameter('swp.model.article.class'));
        $query = $repository->findByCriteria($criteria);
        $partialResult = $query->getResults(
            $criteria->getPagination()->getOffset(),
            $criteria->getPagination()->getItemsPerPage()
        );

        $metaFactory = $this->get('swp_template_engine_context.factory.meta_factory');
        $metaCollection = new MetaCollection();
        $metaCollection->setTotalItemsCount($partialResult->getTotalHits());
        foreach ($partialResult->toArray() as $article) {
            $articleMeta = $metaFactory->create($article);
            if (null !== $articleMeta) {
                $metaCollection->add($articleMeta);
            }
        }

        return $this->render($template, [
            'results' => $metaCollection,
            'criteria' => $criteria,
            'total' => $partialResult->getTotalHits(),
        ]);
    }
}
