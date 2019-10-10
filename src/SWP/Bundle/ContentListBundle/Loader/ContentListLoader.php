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

class ContentListLoader implements LoaderInterface
{
    protected $contentListRepository;

    protected $metaFactory;

    public function __construct(ContentListRepositoryInterface $contentListRepository, MetaFactoryInterface $metaFactory)
    {
        $this->contentListRepository = $contentListRepository;
        $this->metaFactory = $metaFactory;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
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

    public function isSupported(string $type): bool
    {
        return 'contentList' === $type;
    }
}
