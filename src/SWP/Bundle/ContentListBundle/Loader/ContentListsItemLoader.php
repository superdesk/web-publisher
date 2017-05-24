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
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

/**
 * Class ContentListsItemLoader.
 */
class ContentListsItemLoader extends PaginatedLoader implements LoaderInterface
{
    /**
     * @var ContentListRepositoryInterface
     */
    protected $contentListRepository;

    /**
     * @var ContentListItemRepositoryInterface
     */
    protected $contentListItemsRepository;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * ContentListsItemLoader constructor.
     *
     * @param ContentListRepositoryInterface     $contentListRepository
     * @param ContentListItemRepositoryInterface $contentListItemRepository
     */
    public function __construct(
        ContentListRepositoryInterface $contentListRepository,
        ContentListItemRepositoryInterface $contentListItemRepository,
        MetaFactoryInterface $metaFactory
    ) {
        $this->contentListRepository = $contentListRepository;
        $this->contentListItemsRepository = $contentListItemRepository;
        $this->metaFactory = $metaFactory;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Content List Item Loader loads Content List Items from Content List",
     *     parameters={
     *         listName="COLLECTION|name of content list"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return Meta|Meta[]|bool false if meta cannot be loaded, a Meta instance otherwise
     *
     * @throws \Exception
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $criteria = new Criteria();
        if ($responseType === LoaderInterface::COLLECTION) {
            if (array_key_exists('contentListId', $parameters) && is_numeric($parameters['contentListId'])) {
                $contentList = $this->contentListRepository->findOneBy(['id' => $parameters['contentListId']]);
                $criteria->set('contentList', $contentList);
            } elseif (array_key_exists('contentListName', $parameters) && is_string($parameters['contentListName'])) {
                $contentList = $this->contentListRepository->findOneBy(['name' => $parameters['contentListName']]);
                $criteria->set('contentList', $contentList);
            }

            if (!$criteria->has('contentList')) {
                return false;
            }

            if (array_key_exists('sticky', $parameters) && is_bool($parameters['sticky'])) {
                $criteria->set('sticky', $parameters['sticky']);
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
        }
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported(string $type): bool
    {
        return in_array($type, ['contentListItems']);
    }

    private function getItemMeta($item)
    {
        if (null !== $item) {
            return $this->metaFactory->create($item);
        }

        return;
    }
}
