<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;

final class ContentListItemsCountHandler implements SubscribingHandlerInterface
{
    /**
     * @var ContentListItemRepositoryInterface
     */
    private $contentListItemRepository;

    public function __construct(ContentListItemRepositoryInterface $contentListItemRepository)
    {
        $this->contentListItemRepository = $contentListItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'SWP\Bundle\CoreBundle\Model\ContentListItemsCountInterface',
                'method' => 'serializeToJson',
            ],
        ];
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        $data,
        $type,
        $context
    ) {
        $object = $context->getObject();
        $criteria = new Criteria();
        $criteria->set('contentList', $object);

        return $this->contentListItemRepository->getCountByCriteria($criteria, null);
    }
}
