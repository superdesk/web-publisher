<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Loader\PaginatedLoader;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Twig\Cache\CacheBlockTagsCollectorInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

class ContentListsItemLoader extends PaginatedLoader implements LoaderInterface
{
    protected $contentListRepository;

    protected $contentListItemsRepository;

    protected $metaFactory;

    private $cacheBlocksTagsCollector;

    public function __construct(
        ContentListRepositoryInterface $contentListRepository,
        ContentListItemRepositoryInterface $contentListItemRepository,
        MetaFactoryInterface $metaFactory,
        CacheBlockTagsCollectorInterface $cacheBlocksTagsCollector
    ) {
        $this->contentListRepository = $contentListRepository;
        $this->contentListItemsRepository = $contentListItemRepository;
        $this->metaFactory = $metaFactory;
        $this->cacheBlocksTagsCollector = $cacheBlocksTagsCollector;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();
        if (LoaderInterface::COLLECTION === $responseType) {
            if (array_key_exists('contentListId', $parameters) && is_numeric($parameters['contentListId'])) {
                $contentList = $this->contentListRepository->findOneBy(['id' => $parameters['contentListId']]);
                $criteria->set('contentList', $contentList);
            } elseif (array_key_exists('contentListName', $parameters) && is_string($parameters['contentListName'])) {
                $contentList = $this->contentListRepository->findOneBy(['name' => $parameters['contentListName']]);
                $criteria->set('contentList', $contentList);
            } elseif (
                array_key_exists('contentList', $parameters) &&
                $parameters['contentList'] instanceof Meta &&
                $parameters['contentList']->getValues() instanceof ContentListInterface
            ) {
                $criteria->set('contentList', $parameters['contentList']->getValues());
            }

            if (!$criteria->has('contentList')) {
                return false;
            }

            if (array_key_exists('sticky', $parameters) && is_bool($parameters['sticky'])) {
                $criteria->set('sticky', $parameters['sticky']);
            }

            if (isset($withoutParameters['content']) && !empty($withoutParameters['content'])) {
                $criteria->set('exclude_content', $withoutParameters['content']);
            }

            $criteria = $this->applyPaginationToCriteria($criteria, $parameters);
            $contentListItems = $this->contentListItemsRepository->getPaginatedByCriteria($criteria, $criteria->get('order', [
                'sticky' => 'desc',
            ]));
            $itemsCollection = new ArrayCollection($contentListItems->getItems());
            if ($itemsCollection->count() > 0) {
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($contentListItems->getTotalItemCount());
                foreach ($itemsCollection as $item) {
                    $itemMeta = $this->getItemMeta($item);
                    if (null !== $itemMeta) {
                        $metaCollection->add($itemMeta);
                    }
                }
                unset($itemsCollection, $criteria);

                return $metaCollection;
            }
        } elseif (LoaderInterface::SINGLE === $responseType) {
            if (array_key_exists('contentListName', $parameters) && is_string($parameters['contentListName'])) {
                $contentList = $this->contentListRepository->findOneBy(['name' => $parameters['contentListName']]);
                $criteria->set('contentList', $contentList);
            } elseif (
                array_key_exists('contentList', $parameters) &&
                $parameters['contentList'] instanceof Meta &&
                $parameters['contentList']->getValues() instanceof ContentListInterface
            ) {
                $criteria->set('contentList', $parameters['contentList']->getValues());
            }

            if (
                isset($contentList)
                && array_key_exists('article', $parameters)
                && $parameters['article'] instanceof Meta
                && $parameters['article']->getValues() instanceof ArticleInterface
            ) {
                /** @var ContentListItemInterface $currentContentListItem */
                $currentContentListItem = $this->contentListItemsRepository->getQueryByCriteria(new Criteria([
                    'contentList' => $contentList,
                    'content' => $parameters['article']->getValues(),
                ]), [], 'n')->getQuery()->getOneOrNullResult();
                $position = $currentContentListItem->getPosition();
            }

            if (isset($position) && array_key_exists('prev', $parameters) && true === $parameters['prev']) {
                ++$position;
            } elseif (isset($position) && array_key_exists('next', $parameters) && true === $parameters['next']) {
                --$position;
            } else {
                return null;
            }

            return $this->getItemMeta($this->contentListItemsRepository->getOneOrNullByPosition($criteria, $position));
        }
    }

    public function isSupported(string $type): bool
    {
        return in_array($type, ['contentListItems', 'contentListItem']);
    }

    private function getItemMeta($item)
    {
        if (null !== $item) {
            if ($item instanceof ContentListItemInterface) {
                $this->cacheBlocksTagsCollector->addTagToCurrentCacheBlock('a-'.$item->getContent()->getId());
            }

            return $this->metaFactory->create($item);
        }
    }
}
