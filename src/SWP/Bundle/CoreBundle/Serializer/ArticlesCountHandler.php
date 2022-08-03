<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ArticlesCountHandler implements SubscribingHandlerInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ArticlesCountHandler constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(ArticleRepositoryInterface $articleRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
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
                'type' => 'SWP\Bundle\CoreBundle\Model\ArticlesCountInterface',
                'method' => 'serializeToJson',
            ],
        ];
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        $data,
        $type,
        SerializationContext $context
    ) {
        $object = $context->getObject();
        $criteria = new Criteria();
        if ($object instanceof RouteInterface) {
            return 0;
        }

        if ($object instanceof TenantInterface) {
            $tenantCode = $object->getCode();
            $criteria->set('tenantCode', $tenantCode);
            $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

            return $this->articleRepository->countByCriteria($criteria, null);
        }
    }
}
