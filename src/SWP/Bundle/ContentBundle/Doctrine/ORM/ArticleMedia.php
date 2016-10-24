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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Model\ArticleMedia as BaseArticleMedia;
use SWP\Component\Storage\Model\PersistableInterface;

class ArticleMedia extends BaseArticleMedia implements PersistableInterface
{
    /**
     * @var ArrayCollection
     */
    protected $renditions;

    /**
     * ArticleMedia constructor.
     */
    public function __construct()
    {
        $this->renditions = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return ArrayCollection
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * @param ImageRendition $rendition
     */
    public function addRendition(ImageRendition $rendition)
    {
        $this->renditions->add($rendition);
    }

    /**
     * @param ArrayCollection $renditions
     */
    public function setRenditions($renditions)
    {
        $this->renditions = $renditions;
    }
}
