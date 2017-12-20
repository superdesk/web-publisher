<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentListBundle\Remover\ContentListItemsRemoverInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class RemoveItemsListener
{
    /**
     * @var ContentListItemsRemoverInterface
     */
    private $contentListItemsRemover;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var FactoryInterface
     */
    private $contentListItemFactory;

    /**
     * RemoveItemsListener constructor.
     *
     * @param ContentListItemsRemoverInterface $contentListItemsRemover
     * @param ArticleRepositoryInterface       $articleRepository
     * @param FactoryInterface                 $contentListItemFactory
     */
    public function __construct(
        ContentListItemsRemoverInterface $contentListItemsRemover,
        ArticleRepositoryInterface $articleRepository,
        FactoryInterface $contentListItemFactory
    ) {
        $this->contentListItemsRemover = $contentListItemsRemover;
        $this->articleRepository = $articleRepository;
        $this->contentListItemFactory = $contentListItemFactory;
    }

    /**
     * @param GenericEvent $event
     */
    public function onListCriteriaChange(GenericEvent $event)
    {
        $contentList = $event->getSubject();
        if (!$contentList instanceof ContentListInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected argument of type "%s", "%s" given.',
                    ContentListInterface::class,
                    is_object($contentList) ? get_class($contentList) : gettype($contentList)
                )
            );
        }

        if ($contentList->getFilters() !== $event->getArgument('filters')) {
            $this->contentListItemsRemover->removeContentListItems($contentList);
            $filters = $contentList->getFilters();
            $filters = $this->determineLimit($contentList, $filters);

            $articles = $this->articleRepository->getArticlesByCriteria(
                new Criteria($filters),
                ['publishedAt' => 'desc']
            )->getQuery()->getResult();

            $position = 0;
            foreach ($articles as $article) {
                /** @var ContentListItemInterface $contentListItem */
                $contentListItem = $this->contentListItemFactory->create();
                $contentListItem->setContent($article);
                $contentListItem->setPosition($position);
                $contentList->addItem($contentListItem);
                ++$position;
            }
        }
    }

    private function determineLimit(ContentListInterface $contentList, array $filters)
    {
        $limit = 0 === $contentList->getLimit() || null === $contentList->getLimit() ? 100 : $contentList->getLimit();
        $filters['maxResults'] = $limit;

        return $filters;
    }
}
