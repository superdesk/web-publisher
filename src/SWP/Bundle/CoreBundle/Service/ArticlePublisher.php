<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Cloner\ArticleClonerInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var ArticleClonerInterface
     */
    private $articleCloner;

    /**
     * ArticlePublisher constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     * @param EventDispatcherInterface   $eventDispatcher
     * @param ArticleClonerInterface     $articleCloner
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ArticleClonerInterface $articleCloner
    ) {
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->articleCloner = $articleCloner;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(ArticleInterface $article, array $tenants = [])
    {
        foreach ($tenants as $data) {
            /** @var TenantInterface $tenant */
            $tenant = $data['tenant'];

            if ($article->getTenantCode() === $tenant->getCode()) {
                continue;
            }

            if (null !== $this->findArticleByTenantAndCode($tenant->getCode(), $article->getCode())) {
                continue;
            }

            $clonedArticle = $this->articleCloner->clone($article, [
                'tenant' => $tenant,
                'route' => $data['route'],
            ]);
            $this->eventDispatcher->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($clonedArticle));

            $this->articleRepository->persist($clonedArticle);
        }

        $this->articleRepository->flush();
    }

    private function findArticleByTenantAndCode(string $tenantCode, string $code)
    {
        return $existingArticle = $this->articleRepository->findOneBy([
            'tenantCode' => $tenantCode,
            'code' => $code,
        ]);
    }
}
