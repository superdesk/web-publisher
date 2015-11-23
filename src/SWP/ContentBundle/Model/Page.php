<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page.
 */
class Page
{
    const PAGE_TYPE_EXTERNAL_URL = 0;
    const PAGE_TYPE_CONTENT = 1;
    const PAGE_TYPE_CONTAINER = 2;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $externalUrl;

    /**
     * @var string
     */
    protected $contentPath;

    /**
     * @var ArrayCollection
     */
    protected $contents;

    /**
     * @var Page
     */
    protected $parent;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return Page
     */
    public function setType($type = self::PAGE_TYPE_EXTERNAL_URL)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        if ($this->getParent()) {
            return $this->getParent()->getSlug().'/'.$this->slug;
        }

        return $this->slug;
    }

    /**
     * Set templateName.
     *
     * @param string $templateName
     *
     * @return Page
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * Get templateName.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Set externalUrl.
     *
     * @param string $externalUrl
     *
     * @return Page
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;

        return $this;
    }

    /**
     * Get externalUrl.
     *
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * Set contentPath.
     *
     * @param string $contentPath
     *
     * @return Page
     */
    public function setContentPath($contentPath)
    {
        $this->contentPath = $contentPath;

        return $this;
    }

    /**
     * Get contentPath.
     *
     * @return string
     */
    public function getContentPath()
    {
        return $this->contentPath;
    }

    /**
     * Add contents.
     *
     * @param PageContent $content
     *
     * @return Page
     */
    public function addContent(PageContent $content)
    {
        $this->contents->add($content);

        return $this;
    }

    /**
     * Get contents.
     *
     * @return ArrayCollection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Get route name.
     *
     * @return string route name for page
     */
    public function getRouteName()
    {
        $pageName = $this->getName();
        if ($this->getParent()) {
            $pageName = $this->getParent()->getName().'_'.$this->slug;
        }

        return 'swp_page_'.strtolower(str_replace(' ', '_', $pageName));
    }

    /**
     * Gets the value of parent.
     *
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param Page $parent the parent
     *
     * @return self
     */
    protected function setParent(Page $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function hasParent()
    {
        if ($this->parent !== null) {
            return true;
        }

        return false;
    }
}
