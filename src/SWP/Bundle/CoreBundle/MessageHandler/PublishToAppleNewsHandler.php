<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\MessageHandler;

use SWP\Bundle\CoreBundle\AppleNews\AppleNewsPublisher;
use SWP\Bundle\CoreBundle\MessageHandler\Message\PublishToAppleNews;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PublishToAppleNewsHandler implements MessageHandlerInterface
{
    private $appleNewsPublisher;

    private $articleRepository;

    private $tenantRepository;

    public function __construct(
        AppleNewsPublisher $appleNewsPublisher,
        ArticleRepositoryInterface $articleRepository,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->appleNewsPublisher = $appleNewsPublisher;
        $this->articleRepository = $articleRepository;
        $this->tenantRepository = $tenantRepository;
    }

    public function __invoke(PublishToAppleNews $publishToAppleNews)
    {
        $articleId = $publishToAppleNews->getArticleId();
        $tenantId = $publishToAppleNews->getTenantId();

        /** @var ArticleInterface $article */
        $article = $this->articleRepository->findOneBy(['id' => $articleId]);

        if (null === $article) {
            throw new ArticleNotFoundException();
        }

        /** @var TenantInterface $tenant */
        $tenant = $this->tenantRepository->findOneBy(['id' => $tenantId]);

        if (null === $tenant) {
            throw new TenantNotFoundException($tenant->getName());
        }

        $this->appleNewsPublisher->publish($article, $tenant);
        $this->articleRepository->flush();
    }
}
