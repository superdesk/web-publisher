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
namespace SWP\Bundle\AnalyticsBundle\Model;

/**
 * AnalyticsLog-
 */
class AnalyticsLog
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $log;

    /**
     * @var @string
     */
    protected $serverData;

    /**
     * @var @string
     */
    protected $level;

    /**
     * @var @string
     */
    protected $uri;

    /**
     * @var @string
     */
    protected $template;

    /**
     * @var @int
     */
    protected $duration;

    /**
     * @var @int
     */
    protected $memory;

    /**
     * @var \DateTime
     */
    protected $modified;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * Set modified
     *
     * @return AnalyticsLog
     */
    public function setModifiedValue()
    {
        $this->modified = new \DateTime();

        return $this;
    }

    /**
     * Set created
     *
     * @return AnalyticsLog
     */
    public function setCreatedValue()
    {
        $this->modified = new \DateTime();

        $this->created = new \DateTime();

        return $this;
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
     * Set log.
     *
     * @param string $log
     *
     * @return AnalyticsLog
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log.
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set serverData.
     *
     * @param string $serverData
     *
     * @return AnalyticsLog
     */
    public function setServerData($serverData)
    {
        $this->serverData = $serverData;

        return $this;
    }

    /**
     * Get serverData.
     *
     * @return string
     */
    public function getServerData()
    {
        return $this->serverData;
    }

    /**
     * Set level.
     *
     * @param string $level
     *
     * @return AnalyticsLog
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set uri.
     *
     * @param string $uri
     *
     * @return AnalyticsLog
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set template.
     *
     * @param string $template
     *
     * @return AnalyticsLog
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get duration.
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set duration.
     *
     * @param int $duration
     *
     * @return AnalyticsLog
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get memory.
     *
     * @return int
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Set memory.
     *
     * @param string $memory
     *
     * @return AnalyticsLog
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * Set modified.
     *
     * @param \DateTime $modified
     *
     * @return AnalyticsLog
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return AnalyticsLog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
