<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class ArticleSourcesAdder implements ArticleSourcesAdderInterface
{
    /**
     * @var
     */
    private $articleSourceFactory;

    /**
     * @var ArticleSourceServiceInterface
     */
    private $articleSourceService;

    /**
     * @var RepositoryInterface
     */
    private $articleSourceRepository;

    /**
     * ArticleSourcesAdder constructor.
     *
     * @param FactoryInterface              $articleSourceFactory
     * @param ArticleSourceServiceInterface $articleSourceService
     * @param RepositoryInterface           $articleSourceRepository
     */
    public function __construct(
        FactoryInterface $articleSourceFactory,
        ArticleSourceServiceInterface $articleSourceService,
        RepositoryInterface $articleSourceRepository
    ) {
        $this->articleSourceFactory = $articleSourceFactory;
        $this->articleSourceService = $articleSourceService;
        $this->articleSourceRepository = $articleSourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ArticleInterface $article, string $name)
    {
        /** @var ArticleSourceInterface $articleSource */
        $articleSource = $this->articleSourceFactory->create();
        $articleSource->setName($name);

        /** @var ArticleSourceInterface $source */
        if ($source = $this->articleSourceRepository->findOneBy(['name' => $articleSource->getName()])) {
            $article->addSourceReference($this->articleSourceService->getArticleSourceReference($article, $source));

            return;
        }

        $article->addSourceReference($this->articleSourceService->getArticleSourceReference($article, $articleSource));
    }
}
