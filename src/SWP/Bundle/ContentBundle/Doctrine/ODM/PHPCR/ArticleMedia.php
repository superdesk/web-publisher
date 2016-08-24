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
namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Exception\InvalidArgumentException;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia as BaseArticleMedia;

/**
 * Class ArticleMedia.
 */
class ArticleMedia extends BaseArticleMedia implements HierarchyInterface
{
    /**
     * @var mixed
     */
    protected $parent;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var ArrayCollection
     */
    protected $renditions;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->renditions = new ArrayCollection();
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * {@inheritdoc}
     */
    public function addRendition($rendition)
    {
        $this->renditions->add($rendition);
    }

    /**
     * {@inheritdoc}
     */
    public function getRenditions()
    {
        return $this->renditions;
    }
}
