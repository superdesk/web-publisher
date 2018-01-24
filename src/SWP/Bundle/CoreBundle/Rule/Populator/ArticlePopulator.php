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

namespace SWP\Bundle\CoreBundle\Rule\Populator;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ArticlePopulator implements ArticlePopulatorInterface
{
    /**
     * @var ArticleFactoryInterface
     */
    private $articleFactory;

    /**
     * @var FactoryInterface
     */
    private $articleStatisticsFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    public function __construct(
        ArticleFactoryInterface $articleFactory,
        FactoryInterface $articleStatisticsFactory,
        EventDispatcherInterface $eventDispatcher,
        ArticleRepositoryInterface $articleRepository,
        TenantContextInterface $tenantContext
    ) {
        $this->articleFactory = $articleFactory;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->articleRepository = $articleRepository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(PackageInterface $package, array $tenants): void
    {
        /** @var TenantInterface $tenant */
        foreach ($tenants as $tenant) {
            $this->tenantContext->setTenant($tenant);

            if (null !== ($article = $this->findArticleByTenantAndCode($tenant->getCode(), $package->getGuid()))) {
                continue;
            }

            /** @var ArticleInterface $article */
            $article = $this->articleFactory->createFromPackage($package);
            /** @var ArticleStatisticsInterface $articleStatistics */
            $articleStatistics = $this->articleStatisticsFactory->create();
            $articleStatistics->setArticle($article);

            $this->articleRepository->persist($articleStatistics);

            $this->eventDispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($article));

            $article->setPackage($package);
            $article->setArticleStatistics($articleStatistics);
            $this->articleRepository->persist($article);
            $this->articleRepository->flush();

            $this->eventDispatcher->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $package, ArticleEvents::PRE_CREATE));
            $this->articleRepository->flush();
        }
    }

    /**
     * @param string $tenantCode
     * @param string $code
     *
     * @return TenantInterface
     */
    private function findArticleByTenantAndCode(string $tenantCode, string $code)
    {
        return $this->articleRepository->findOneBy([
            'tenantCode' => $tenantCode,
            'code' => $code,
        ]);
    }
}
