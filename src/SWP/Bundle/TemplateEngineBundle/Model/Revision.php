<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Model;


class Revision
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $originId;

    /**
     * @var int
     */
    protected $revisionId;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var \DateTime
     */
    private $createdAt;
    /**
     * @var boolean
     */
    private $published;

    /**
     * @var string
     */
    private $condition;

    public function __construct()
    {
        $this->published = false;
    }

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
     * Set originId
     *
     * @param integer $originId
     *
     * @return Revision
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;

        return $this;
    }

    /**
     * Get originId
     *
     * @return integer
     */
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * Set revisionId
     *
     * @param integer $revisionId
     *
     * @return Revision
     */
    public function setRevisionId($revisionId)
    {
        $this->revisionId = $revisionId;

        return $this;
    }

    /**
     * Get revisionId
     *
     * @return integer
     */
    public function getRevisionId()
    {
        return $this->revisionId;
    }

    /**
     * Set className
     *
     * @param string $className
     *
     * @return Revision
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Revision
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Revision
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set condition
     *
     * @param string $condition
     *
     * @return Revision
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
