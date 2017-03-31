<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Service;

use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\ReplaceFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyMatcher;
use DeepCopy\Matcher\PropertyNameMatcher;
use DeepCopy\Matcher\PropertyTypeMatcher;
use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Factory\ClonerFactoryInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleMedia;
use SWP\Bundle\CoreBundle\Model\Image;
use SWP\Bundle\CoreBundle\Model\Organization;
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
     * @var ClonerFactoryInterface
     */
    private $clonerFactory;

    /**
     * ArticlePublisher constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     * @param EventDispatcherInterface   $eventDispatcher
     * @param ClonerFactoryInterface     $clonerFactory
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ClonerFactoryInterface $clonerFactory
    ) {
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->clonerFactory = $clonerFactory;
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

            $clonedArticle = $this->cloneArticle($article, $tenant);
            $clonedArticle->setRoute($data['route']);
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

    private function cloneArticle(ArticleInterface $article, TenantInterface $tenant)
    {
        $cloner = $this->clonerFactory->create();
        $cloner->skipUncloneable();
        $callback = function ($currentValue) use ($tenant) {
            return $tenant->getCode();
        };

        $cloner->addFilter(new KeepFilter(), new PropertyTypeMatcher(Organization::class));
        $cloner->addFilter(new SetNullFilter(), new PropertyNameMatcher('id'));
        $cloner->addFilter(new DoctrineCollectionFilter(), new PropertyTypeMatcher(Collection::class));
        $cloner->addFilter(new ReplaceFilter($callback), new PropertyMatcher(Article::class, 'tenantCode'));
        $cloner->addFilter(new ReplaceFilter($callback), new PropertyMatcher(ArticleMedia::class, 'tenantCode'));
        $cloner->addFilter(new ReplaceFilter($callback), new PropertyMatcher(Image::class, 'tenantCode'));

        return $cloner->copy($article);
    }
}
