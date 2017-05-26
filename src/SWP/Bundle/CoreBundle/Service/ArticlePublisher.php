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
use SWP\Bundle\CoreBundle\Model\CompositePublishActionInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\PublishDestinationInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
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
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ArticlePublisher constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     * @param EventDispatcherInterface   $eventDispatcher
     * @param ArticleFactoryInterface    $articleFactory
     * @param TenantContextInterface     $tenantContext
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ArticleFactoryInterface $articleFactory,
        TenantContextInterface $tenantContext
    ) {
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->articleFactory = $articleFactory;
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
                    $this->eventDispatcher->dispatch(ArticleEvents::UNPUBLISH, new ArticleEvent($article));
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
            /** @var ArticleInterface $article */
            $article = $this->articleFactory->createFromPackage($package);
            $this->eventDispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($article));

            if (null !== ($existingArticle = $this->findArticleByTenantAndCode(
                    $destination->getTenant()->getCode(),
                    $article->getCode())
                )) {
                $article->setRoute($destination->getRoute());
                $article->setPublishedFBIA($destination->isFbia());
                $this->dispatchEvents($existingArticle, $package);

                continue;
            }

            $article->setPackage($package);
            $article->setRoute($destination->getRoute());
            $article->setPublishedFBIA($destination->isFbia());
            $this->dispatchEvents($article, $package);
            $this->articleRepository->persist($article);
        }

        $this->articleRepository->flush();
    }

    private function findArticleByTenantAndCode(string $tenantCode, string $code)
    {
        return $this->articleRepository->findOneBy([
            'tenantCode' => $tenantCode,
            'code' => $code,
        ]);
    }

    private function dispatchEvents(ArticleInterface $article, PackageInterface $package)
    {
        $this->eventDispatcher->dispatch(ArticleEvents::PUBLISH, new ArticleEvent($article));
        $this->eventDispatcher->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $package));
    }
}
