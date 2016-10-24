<?php

declare(strict_types=1);

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

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;

interface TreeAwareRouteInterface
{
    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * @return RouteInterface
     */
    public function getRoot(): RouteInterface;

    /**
     * @return RouteInterface|null
     */
    public function getParent();

    /**
     * @param RouteInterface|null $parent
     */
    public function setParent(RouteInterface $parent = null);

    /**
     * @return Collection|RouteInterface[]
     */
    public function getChildren();

    /**
     * @param RouteInterface $route
     *
     * @return bool
     */
    public function hasChild(RouteInterface $route): bool;

    /**
     * @param RouteInterface $route
     */
    public function addChild(RouteInterface $route);

    /**
     * @param RouteInterface $route
     */
    public function removeChild(RouteInterface $route);

    /**
     * @return int
     */
    public function getLeft(): int;

    /**
     * @param int $left
     */
    public function setLeft(int $left);

    /**
     * @return int
     */
    public function getRight(): int;

    /**
     * @param int $right
     */
    public function setRight(int $right);

    /**
     * @return int
     */
    public function getLevel(): int;

    /**
     * @param int $level
     */
    public function setLevel(int $level);
}
