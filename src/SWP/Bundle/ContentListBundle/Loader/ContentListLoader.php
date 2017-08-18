<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Loader;

use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * Class ContentListLoader.
 */
class ContentListLoader implements LoaderInterface
{
    /**
     * @var ContentListRepositoryInterface
     */
    protected $contentListRepository;

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * ContentListLoader constructor.
     *
     * @param ContentListRepositoryInterface $contentListRepository
     * @param MetaFactoryInterface           $metaFactory
     */
    public function __construct(ContentListRepositoryInterface $contentListRepository, MetaFactoryInterface $metaFactory)
    {
        $this->contentListRepository = $contentListRepository;
        $this->metaFactory = $metaFactory;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Content List Loader loads Content List",
     *     parameters={
     *         listName="SINGLE|name of content list",
     *         listId="SINGLE|name of content list"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return Meta|bool false if meta cannot be loaded, a Meta instance otherwise
     *
     * @throws \Exception
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::SINGLE)
    {
        if (LoaderInterface::SINGLE === $responseType) {
            $contentList = null;
            $criteria = new Criteria();
            if (array_key_exists('contentListId', $parameters) && is_numeric($parameters['contentListId'])) {
                $criteria->set('id', $parameters['contentListId']);
            } elseif (array_key_exists('contentListName', $parameters) && is_string($parameters['contentListName'])) {
                $criteria->set('name', $parameters['contentListName']);
            } else {
                return false;
            }

            $contentList = $this->contentListRepository->getQueryByCriteria($criteria, [], 'c')->getQuery()->getOneOrNullResult();

            if (null !== $contentList) {
                return $this->metaFactory->create($contentList);
            }
        }

        return false;
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
        return in_array($type, ['contentList']);
    }
}
