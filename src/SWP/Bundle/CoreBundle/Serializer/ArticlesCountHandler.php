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
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\Storage\Model\PersistableInterface;

final class ArticlesCountHandler implements SubscribingHandlerInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * ArticlesCountHandler constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'SWP\Bundle\CoreBundle\Model\ArticlesCountInterface',
                'method' => 'serializeToJson',
            ),
        );
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        $data,
        $type,
        $context
    ) {
        $object = $context->getObject();
        $criteria = new Criteria();
        if ($object instanceof PersistableInterface && $object instanceof RouteInterface) {
            $id = $object->getId();
            $criteria->set('route', $id);
            $count = $this->articleRepository->countByCriteria($criteria, null);

            return $count;
        } elseif ($object instanceof TenantInterface) {
            $tenantCode = $object->getCode();
            $criteria->set('tenantCode', $tenantCode);
            $count = $this->articleRepository->countByCriteria($criteria, null);

            return $count;
        }
    }
}
