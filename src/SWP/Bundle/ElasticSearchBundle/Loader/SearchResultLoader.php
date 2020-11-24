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
    public const MAX_RESULTS = 10000;

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
     * @var array
     */
    private $extraFields;

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
        string $modelClass,
        array $extraFields
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->metaFactory = $metaFactory;
        $this->tenantContext = $tenantContext;
        $this->modelClass = $modelClass;
        $this->extraFields = $extraFields;
    }

    /**
     * {@inheritdoc}
     */
    public function load($metaType, $withParameters = [], $withoutParameters = [], $responseType = self::COLLECTION)
    {
        if (isset($withParameters['order']) && 2 === count($withParameters['order'])) {
            $withParameters['sort'] = [$withParameters['order'][0] => $withParameters['order'][1]];
            unset($withParameters['order']);
        }

        $criteria = Criteria::fromQueryParameters($withParameters['term'] ?? '', $withParameters);

        /** @var ArticleRepository $repository */
        $repository = $this->repositoryManager->getRepository($this->modelClass);
        $query = $repository->findByCriteria($criteria, $this->extraFields);

        $pagination = $criteria->getPagination();
        $metaCollection = new MetaCollection();

        if (($pagination->getCurrentPage() * $pagination->getItemsPerPage()) > (self::MAX_RESULTS + $pagination->getItemsPerPage())) {
            return $metaCollection;
        }

        $partialResult = $query->getResults(
            $pagination->getOffset(),
            $pagination->getItemsPerPage()
        );

        $metaCollection->setTotalItemsCount($query->getTotalHits());
        foreach ($partialResult->toArray() as $article) {
            if (null !== ($articleMeta = $this->metaFactory->create($article))) {
                $metaCollection->add($articleMeta);
            }
        }

        $metaCollection->suggestedTerm = $repository->getSuggestedTerm($criteria->getTerm());

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
