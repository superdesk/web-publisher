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
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\Common\Exception\UnexpectedTypeException;
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
                /** @var TenantInterface $tenant */
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
    public function publish(PackageInterface $package, array $tenants = [])
    {
        foreach ($tenants as $data) {
            $this->validateData($data);
            /** @var TenantInterface $tenant */
            $tenant = $data['tenant'];
            $this->tenantContext->setTenant($tenant);
            /** @var ArticleInterface $article */
            $article = $this->articleFactory->createFromPackage($package);
            $this->eventDispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($article));

            if (null !== ($existingArticle = $this->findArticleByTenantAndCode(
                    $tenant->getCode(),
                    $article->getCode())
                )) {
                $article->setRoute($data['route']);
                $this->dispatchEvents($existingArticle, $package);

                continue;
            }
// dispatch event so it will add article immediately to content bucket if its not added yet
            // create content bucket listener
            // on post create or post publish add article to content bucket
            $article->setRoute($data['route']);
            $this->dispatchEvents($article, $package);
            $this->articleRepository->persist($article);
            $package->addArticle($article);
        }

        $this->articleRepository->flush();
    }

    private function validateData(array $config = [])
    {
        if (!isset($config['tenant']) || !isset($config['route'])) {
            throw new \InvalidArgumentException('Wrong data passed!');
        }

        if (isset($config['tenant']) && !$config['tenant'] instanceof TenantInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($config['tenant']) ? get_class($config['tenant']) : gettype($config['tenant']),
                TenantInterface::class);
        }

        if (isset($config['route']) && !$config['route'] instanceof RouteInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($config['route']) ? get_class($config['route']) : gettype($config['tenant']),
                RouteInterface::class);
        }
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
