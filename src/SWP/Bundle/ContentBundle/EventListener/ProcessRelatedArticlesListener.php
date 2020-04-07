<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use DateTime;
use DateTimeZone;

class ProcessRelatedArticlesListener
{
    /**
     * @var FactoryInterface
     */
    private $relatedArticleFactory;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    public function __construct(
        FactoryInterface $relatedArticleFactory,
        ArticleRepositoryInterface $articleRepository
    ) {
        $this->relatedArticleFactory = $relatedArticleFactory;
        $this->articleRepository = $articleRepository;
    }

    public function onArticleCreate(ArticleEvent $event): void
    {
        $package = $event->getPackage();
        $article = $event->getArticle();
        if (isset($article->getExtra()['republish']) && $article->getExtra()['republish'] == True) {
            $article->setPublishedAt(new DateTime('now'));
        }
        $this->removeOldRelatedArticles($article);

        $relatedItemsGroups = $package->getItems()->filter(static function ($item) {
            return ItemInterface::TYPE_TEXT === $item->getType();
        });

        if (null === $package || (null !== $package && 0 === \count($relatedItemsGroups))) {
            return;
        }

        $related = $this->reorderItems($relatedItemsGroups->toArray());

        foreach ($related as $item) {
            if (null === ($existingArticle = $this->articleRepository->findOneBy(['code' => $item->getGuid()]))) {
                continue;
            }

            $relatedArticle = $this->relatedArticleFactory->create();

            $relatedArticle->setArticle($existingArticle);
            $article->addRelatedArticle($relatedArticle);
        }
    }

    private function removeOldRelatedArticles(ArticleInterface $article): void
    {
        foreach ($article->getRelatedArticles() as $relatedArticle) {
            $article->removeRelatedArticle($relatedArticle);
        }
    }

    private function reorderItems(array $relatedItemsGroups): array
    {
        $related = [];
        foreach ($relatedItemsGroups as $item) {
            $result = explode('--', $item->getName());

            if (!isset($result[1])) {
                continue;
            }

            $position = (int) $result[1];
            $related[$position] = $item;
        }

        krsort($related);

        return $related;
    }
}
