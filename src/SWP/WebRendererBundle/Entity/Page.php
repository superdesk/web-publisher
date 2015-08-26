<?php

namespace SWP\WebRendererBundle\Entity;

/**
 * Page
 */
class Page
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $externalUrl;

    /**
     * @var string
     */
    private $contentPath;

    /**
     * @var array
     */
    private $articles;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Page
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set slug
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
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set templateName
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
     * Get templateName
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Set externalUrl
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
     * Get externalUrl
     *
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * Set contentPath
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
     * Get contentPath
     *
     * @return string
     */
    public function getContentPath()
    {
        return $this->contentPath;
    }

    /**
     * Set articles
     *
     * @param array $articles
     *
     * @return Page
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Get articles
     *
     * @return array
     */
    public function getArticles()
    {
        return $this->articles;
    }
}

