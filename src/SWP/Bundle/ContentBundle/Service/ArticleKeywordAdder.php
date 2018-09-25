<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Factory\KeywordFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\KeywordInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class ArticleKeywordAdder implements ArticleKeywordAdderInterface
{
    /**
     * @var KeywordFactoryInterface
     */
    private $keywordFactory;

    /**
     * @var RepositoryInterface
     */
    private $articleKeywordRepository;

    public function __construct(KeywordFactoryInterface $keywordFactory, RepositoryInterface $articleKeywordRepository)
    {
        $this->keywordFactory = $keywordFactory;
        $this->articleKeywordRepository = $articleKeywordRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ArticleInterface $article, string $name)
    {
        /** @var KeywordInterface $keyword */
        if ($keyword = $this->articleKeywordRepository->findOneBy(['name' => $name])) {
            $article->addKeyword($keyword);

            return;
        }

        $article->addKeyword($this->keywordFactory->create($name));
    }
}
