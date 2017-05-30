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

namespace SWP\Bundle\ElasticSearchBundle\Loader;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Repository\ArticleRepository;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class SearchResultLoader implements LoaderInterface
{
    /**
     * @var RepositoryManagerInterface
     */
    private $repositoryManager;

    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * SearchResultLoader constructor.
     *
     * @param RepositoryManagerInterface $repositoryManager
     * @param MetaFactoryInterface       $metaFactory
     * @param TenantContextInterface     $tenantContext
     * @param string                     $modelClass
     */
    public function __construct(
        RepositoryManagerInterface $repositoryManager,
        MetaFactoryInterface $metaFactory,
        TenantContextInterface $tenantContext,
        string $modelClass
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->metaFactory = $metaFactory;
        $this->tenantContext = $tenantContext;
        $this->modelClass = $modelClass;
    }

    /**
     * {@inheritdoc}
     */
    public function load($metaType, $parameters = [], $responseType = self::COLLECTION)
    {
        if (isset($parameters['order']) && count($parameters['order']) == 2) {
            $parameters['sort'] = [$parameters['order'][0] => $parameters['order'][1]];
            unset($parameters['order']);
        }

        $criteria = Criteria::fromQueryParameters(isset($parameters['term']) ? $parameters['term'] : '', $parameters);

        /** @var ArticleRepository $repository */
        $repository = $this->repositoryManager->getRepository($this->modelClass);
        $query = $repository->findByCriteria($criteria);
        $partialResult = $query->getResults(
            $criteria->getPagination()->getOffset(),
            $criteria->getPagination()->getItemsPerPage()
        );

        $metaCollection = new MetaCollection();
        $metaCollection->setTotalItemsCount($partialResult->getTotalHits());
        foreach ($partialResult->toArray() as $article) {
            if (null !== ($articleMeta = $this->metaFactory->create($article))) {
                $metaCollection->add($articleMeta);
            }
        }

        return $metaCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(string $type): bool
    {
        return in_array($type, ['searchResults']);
    }
}
