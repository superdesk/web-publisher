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

namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route as BaseRoute;

class Route extends BaseRoute implements RouteObjectInterface
{
    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $articlesTemplateName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $cacheTimeInSeconds = 0;

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticlesTemplateName()
    {
        return $this->articlesTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticlesTemplateName($articlesTemplateName)
    {
        $this->articlesTemplateName = $articlesTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getCacheTimeInSeconds()
    {
        return $this->cacheTimeInSeconds;
    }

    /**
     * @param int $cacheTimeInSeconds
     */
    public function setCacheTimeInSeconds($cacheTimeInSeconds)
    {
        $this->cacheTimeInSeconds = $cacheTimeInSeconds;
    }

    public function getRouteName()
    {
        return $this->getId();
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        // TODO: Implement isRoot() method.
    }

    /**
     * @return RouteInterface
     */
    public function getRoot(): RouteInterface
    {
        // TODO: Implement getRoot() method.
    }

    /**
     * @return RouteInterface|null
     */
    public function getParent()
    {
        // TODO: Implement getParent() method.
    }

    /**
     * @param RouteInterface|null $parent
     */
    public function setParent(RouteInterface $parent = null)
    {
        // TODO: Implement setParent() method.
    }

    /**
     * @param RouteInterface $route
     *
     * @return bool
     */
    public function hasChild(RouteInterface $route): bool
    {
        // TODO: Implement hasChild() method.
    }

    /**
     * @param RouteInterface $route
     */
    public function addChild(RouteInterface $route)
    {
        // TODO: Implement addChild() method.
    }

    /**
     * @param RouteInterface $route
     */
    public function removeChild(RouteInterface $route)
    {
        // TODO: Implement removeChild() method.
    }

    /**
     * @return int
     */
    public function getLeft(): int
    {
        // TODO: Implement getLeft() method.
    }

    /**
     * @param int $left
     */
    public function setLeft(int $left)
    {
        // TODO: Implement setLeft() method.
    }

    /**
     * @return int
     */
    public function getRight(): int
    {
        // TODO: Implement getRight() method.
    }

    /**
     * @param int $right
     */
    public function setRight(int $right)
    {
        // TODO: Implement setRight() method.
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        // TODO: Implement getLevel() method.
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level)
    {
        // TODO: Implement setLevel() method.
    }
}
