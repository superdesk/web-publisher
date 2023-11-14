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
use SWP\Bundle\ContentListBundle\Services\ContentListServiceInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\CoreBundle\Model\CompositePublishActionInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\PublishDestinationInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepository;
use SWP\Component\Bridge\Events;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
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
     * @var ContentListRepositoryInterface
     */
    private $contentListRepository;

    /**
     * @var ContentListItemRepository
     */
    private $contentListItemRepository;

    /**
     * @var ContentListServiceInterface
     */
    private $contentListService;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ArticleFactoryInterface $articleFactory,
        FactoryInterface $articleStatisticsFactory,
        TenantContextInterface $tenantContext,
        ContentListRepositoryInterface $contentListRepository,
        ContentListItemRepository $contentListItemRepository,
        ContentListServiceInterface $contentListService
    ) {
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->articleFactory = $articleFactory;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->tenantContext = $tenantContext;
        $this->contentListRepository = $contentListRepository;
        $this->contentListItemRepository = $contentListItemRepository;
        $this->contentListService = $contentListService;
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(PackageInterface $package, array $tenants = []): void
    {
        foreach ($package->getArticles() as $article) {
            foreach ($tenants as $tenant) {
                /* @var TenantInterface $tenant */
                $this->tenantContext->setTenant($tenant);
                if ($article->getTenantCode() === $tenant->getCode()) {
                    $this->eventDispatcher->dispatch(new ArticleEvent($article, null, ArticleEvents::UNPUBLISH), ArticleEvents::UNPUBLISH);
                }
            }
        }

        $this->articleRepository->flush();
    }

    public function publish(PackageInterface $package, CompositePublishActionInterface $action): void
    {
        $originalRequestTenant = $this->tenantContext->getTenant();
        /** @var PublishDestinationInterface $destination */
        foreach ($action->getDestinations() as $destination) {
            $this->tenantContext->setTenant($destination->getTenant());

            /* @var ArticleInterface $article */
            if (null !== ($article = $this->findArticleByTenantAndCode($destination->getTenant()->getCode(), $package->getGuid()))) {
                $originalRoute = null;
                if ($article->getRoute()->getId() !== $destination->getRoute()->getId()) {
                    $originalRoute = $article->getRoute();
                }
                $article->setRoute($destination->getRoute());
                $article->setPublishedFBIA($destination->isPublishedFbia());
                $article->setPaywallSecured($destination->isPaywallSecured());
                $article->setPublishedToAppleNews($destination->isPublishedToAppleNews());
                $this->eventDispatcher->dispatch(new GenericEvent($article), Events::SWP_VALIDATION);
                $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::PRE_UPDATE, $article->getRoute()), ArticleEvents::PRE_UPDATE);
                $this->articleRepository->flush();

                if ($destination->isPublished()) {
                    $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::PUBLISH), ArticleEvents::PUBLISH);
                    $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::POST_PUBLISH), ArticleEvents::POST_PUBLISH);
                }

                $this->addToContentLists($destination->getContentLists(), $article);

                $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::POST_UPDATE), ArticleEvents::POST_UPDATE);
                $this->articleRepository->flush();

                continue;
            }

            /** @var ArticleInterface $article */
            $article = $this->articleFactory->createFromPackage($package);
            /** @var ArticleStatisticsInterface $articleStatistics */
            $articleStatistics = $this->articleStatisticsFactory->create();
            $articleStatistics->setArticle($article);
            $this->articleRepository->persist($articleStatistics);
            $this->eventDispatcher->dispatch(new GenericEvent($article), Events::SWP_VALIDATION);
            $package->addArticle($article);
            $route = $destination->getRoute();
            if (null !== $route) {
                $route->setArticlesUpdatedAt(new \DateTime());
                $article->setRoute($route);
            }
            $article->setPublishedFBIA($destination->isPublishedFbia());
            $article->setPaywallSecured($destination->isPaywallSecured());
            $article->setPublishedToAppleNews($destination->isPublishedToAppleNews());
            $article->setArticleStatistics($articleStatistics);

            $this->articleRepository->persist($article);
            $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::PRE_CREATE), ArticleEvents::PRE_CREATE);
            $this->articleRepository->flush();
            $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::POST_CREATE), ArticleEvents::POST_CREATE);

            if ($destination->isPublished()) {
                $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::PUBLISH), ArticleEvents::PUBLISH);
                $this->eventDispatcher->dispatch(new ArticleEvent($article, $package, ArticleEvents::POST_PUBLISH), ArticleEvents::POST_PUBLISH);
            }

            $this->addToContentLists($destination->getContentLists(), $article);

            $this->articleRepository->flush();
        }
        $this->tenantContext->setTenant($originalRequestTenant);
    }

    private function addToContentLists(array $contentListsPositions, ArticleInterface $article): void
    {
        foreach ($contentListsPositions as $contentListsPosition) {
            if (!is_int($contentListsPosition['id'])) {
                return;
            }

            $contentList = $this->contentListRepository->findListById($contentListsPosition['id']);
            if (null === $contentList) {
                continue;
            }

            $existingItemOnList = $this->contentListItemRepository->findItemByArticleAndList($article, $contentList, ContentListInterface::TYPE_MANUAL);
            if (null === $existingItemOnList) {
                $this->contentListService->addArticleToContentList(
                    $contentList,
                    $article,
                    $contentListsPosition['position']
                );
            }
        }
    }

    /**
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
