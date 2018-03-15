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

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\CoreBundle\Model\CompositePublishActionInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\PublishDestinationInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ArticlePublisher implements ArticlePublisherInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ArticleFactoryInterface
     */
    private $articleFactory;

    /**
     * @var FactoryInterface
     */
    private $articleStatisticsFactory;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ArticlePublisher constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     * @param EventDispatcherInterface   $eventDispatcher
     * @param ArticleFactoryInterface    $articleFactory
     * @param FactoryInterface           $articleStatisticsFactory
     * @param TenantContextInterface     $tenantContext
     */
    public function __construct(ArticleRepositoryInterface $articleRepository, EventDispatcherInterface $eventDispatcher, ArticleFactoryInterface $articleFactory, FactoryInterface $articleStatisticsFactory, TenantContextInterface $tenantContext)
    {
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->articleFactory = $articleFactory;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(PackageInterface $package, array $tenants = [])
    {
        foreach ($package->getArticles() as $article) {
            foreach ($tenants as $tenant) {
                /* @var TenantInterface $tenant */
                $this->tenantContext->setTenant($tenant);
                if ($article->getTenantCode() === $tenant->getCode()) {
                    $this->eventDispatcher->dispatch(ArticleEvents::UNPUBLISH, new ArticleEvent($article, null, ArticleEvents::UNPUBLISH));
                }
            }
        }

        $this->articleRepository->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function publish(PackageInterface $package, CompositePublishActionInterface $action)
    {
        /** @var PublishDestinationInterface $destination */
        foreach ($action->getDestinations() as $destination) {
            $this->tenantContext->setTenant($destination->getTenant());

            /* @var ArticleInterface $existingArticle */
            if (null !== ($article = $this->findArticleByTenantAndCode($destination->getTenant()->getCode(), $package->getGuid()))) {
                $article->setRoute($destination->getRoute());
                $article->setPublishedFBIA($destination->isFbia());
                $this->eventDispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($article));
                $this->dispatchEvents($article, $package);

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
            $article->setRoute($destination->getRoute());
            $article->setPublishedFBIA($destination->isFbia());
            $article->setArticleStatistics($articleStatistics);
            $this->articleRepository->persist($article);

            if ($destination->isPublished()) {
                $this->eventDispatcher->dispatch(ArticleEvents::PUBLISH, new ArticleEvent($article, null, ArticleEvents::PUBLISH));
            }

            $this->eventDispatcher->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $package, ArticleEvents::PRE_CREATE));
        }

        $this->articleRepository->flush();
    }

    /**
     * @param string $tenantCode
     * @param string $code
     *
     * @return object
     */
    private function findArticleByTenantAndCode(string $tenantCode, string $code)
    {
        return $this->articleRepository->findOneBy([
            'tenantCode' => $tenantCode,
            'code' => $code,
        ]);
    }
}
