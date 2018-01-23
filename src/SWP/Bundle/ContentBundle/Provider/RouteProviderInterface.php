<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use Symfony\Cmf\Component\Routing\RouteProviderInterface as CmfRouteProviderInterface;

interface RouteProviderInterface extends CmfRouteProviderInterface
{
    /**
     * Gets routes repository.
     *
     * @return RouteRepositoryInterface
     */
    public function getRepository(): RouteRepositoryInterface;

    /**
     * Gets the base route.
     *
     * @return RouteInterface
     */
    public function getBaseRoute();

    /**
     * Gets one route by id.
     *
     * @param string $id
     *
     * @return RouteInterface|void
     */
    public function getOneById($id);

    /**
     * @param string $staticPrefix
     *
     * @return RouteInterface|null
     */
    public function getOneByStaticPrefix($staticPrefix);

    /**
     * @param array $candidates
     * @param array $orderBy
     *
     * @return array
     */
    public function getByStaticPrefix(array $candidates, array $orderBy = []): array;

    /**
     * @param array $candidates
     * @param array $orderBy
     *
     * @return array
     */
    public function getChildrensByStaticPrefix(array $candidates, array $orderBy = []): array;

    /**
     * @param array $candidates
     *
     * @return array|null
     */
    public function getWithChildrensByStaticPrefix(array $candidates): ?array;
}
