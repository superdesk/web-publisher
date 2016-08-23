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
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\PHPCR\Exception\InvalidArgumentException;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use SWP\Bundle\ContentBundle\Model\Article as BaseArticle;

class Article extends BaseArticle implements HierarchyInterface
{
    /**
     * PHPCR parent document.
     *
     * @var object
     */
    protected $parent;

    /**
     * Child article documents.
     *
     * @var Collection
     */
    protected $children;

    /**
     * @var Collection
     */
    protected $media;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->media = new ArrayCollection();
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
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Collection
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Collection $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }
}
