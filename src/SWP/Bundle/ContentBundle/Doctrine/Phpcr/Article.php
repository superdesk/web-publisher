<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Exception\InvalidArgumentException;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Bundle\ContentBundle\Model\Article as BaseArticle;

class Article extends BaseArticle implements HierarchyInterface
{
    /**
     * @var Route
     */
    protected $route;

    /**
     * PHPCR parent document.
     *
     * @var object
     */
    protected $parent;

    public function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent($parent)
    {
        $this->setParentDocument($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getParentDocument();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentDocument()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentDocument($parent)
    {
        if (!is_object($parent)) {
            throw new InvalidArgumentException('Parent must be an object '.gettype($parent).' given.');
        }

        $this->parent = $parent;
    }
}
