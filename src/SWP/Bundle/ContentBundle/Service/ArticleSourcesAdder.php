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
     * @var RepositoryInterface
     */
    private $articleSourceRepository;

    /**
     * ArticleSourcesAdder constructor.
     *
     * @param FactoryInterface    $articleSourceFactory
     * @param RepositoryInterface $articleSourceRepository
     */
    public function __construct(
        FactoryInterface $articleSourceFactory,
        RepositoryInterface $articleSourceRepository
    ) {
        $this->articleSourceFactory = $articleSourceFactory;
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
        if ($source = $this->articleSourceRepository->findOneBy([
                'name' => $articleSource->getName(),
            ])) {
            $article->addSource($source);

            return;
        }

        $article->addSource($articleSource);
    }
}
