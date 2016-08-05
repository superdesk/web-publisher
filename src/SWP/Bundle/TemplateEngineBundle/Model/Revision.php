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

abstract class Revision
{
    const STATE_PUBLISHED = 0;
    const STATE_UNPUBLISHED = 1;
    const STATE_ARCHIVED = 2;

    /**
     * @var int
     */
    protected $originId;

    /**
     * @var int
     */
    private $state;

    public function __construct()
    {
        $this->state = self::STATE_PUBLISHED;
    }

    /**
     * Get id.
     *
     * @return int
     */
    abstract public function getId();

    /**
     * Get name.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return self
     */
    abstract public function setName($name);

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
     * @return Revision
     */
    public function createNextRevision()
    {
        return clone $this;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Revision
     */
    public function setState($state)
    {
        if ($state !== self::STATE_ARCHIVED &&
            $state !== self::STATE_PUBLISHED &&
            $state !== self::STATE_UNPUBLISHED)
        {
            throw new \Exception('Invalid state '.$state);
        }

        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param Revision $predecessor
     */
    public function onPublished(Revision $predecessor)
    {
    }
}
