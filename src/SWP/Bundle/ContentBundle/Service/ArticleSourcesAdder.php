<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Doctrine\ArticleSourceRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class ArticleSourcesAdder implements ArticleSourcesAdderInterface
{
    /**
     * @var
     */
    private $articleSourceFactory;

    /**
     * @var ArticleSourceRepositoryInterface
     */
    private $articleSourceRepository;

    /**
     * ArticleSourcesAdder constructor.
     *
     * @param FactoryInterface                 $articleSourceFactory
     * @param ArticleSourceRepositoryInterface $articleSourceRepository
     */
    public function __construct(
        FactoryInterface $articleSourceFactory,
        ArticleSourceRepositoryInterface $articleSourceRepository
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
