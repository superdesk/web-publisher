<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleSourcesAdderInterface;
use SWP\Bundle\CoreBundle\Model\ArticleSourceInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class TenantAwareArticleSourcesAdder implements ArticleSourcesAdderInterface
{
    /**
     * @var FactoryInterface
     */
    private $articleSourceFactory;

    /**
     * @var RepositoryInterface
     */
    private $articleSourceRepository;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ArticleSourcesAdder constructor.
     *
     * @param FactoryInterface       $articleSourceFactory
     * @param RepositoryInterface    $articleSourceRepository
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(
        FactoryInterface $articleSourceFactory,
        RepositoryInterface $articleSourceRepository,
        TenantContextInterface $tenantContext
    ) {
        $this->articleSourceFactory = $articleSourceFactory;
        $this->articleSourceRepository = $articleSourceRepository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ArticleInterface $article, string $name)
    {
        /** @var ArticleSourceInterface $articleSource */
        $articleSource = $this->articleSourceFactory->create();
        $articleSource->setName($name);
        $articleSource->setTenantCode($this->tenantContext->getTenant()->getCode());

        /** @var ArticleSourceInterface $source */
        if ($source = $this->articleSourceRepository->findOneBy([
            'name' => $articleSource->getName(),
            'tenantCode' => $articleSource->getTenantCode(),
        ])) {
            $article->addSource($source);

            return;
        }

        $article->addSource($articleSource);
    }
}
