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
namespace SWP\AnalyticsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * AnalyticsLog
 */
class AnalyticsLog
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $log;

    /**
     * @var @string
     */
    private $serverData;

    /**
     * @var @string
     */
    private $level;

    /**
     * @var datetime
     */
    private $modified;

    /**
     * @var datetime
     */
    private $created;

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
     * Set log
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
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set serverData
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
     * Get serverData
     *
     * @return string
     */
    public function getServerData()
    {
        return $this->serverData;
    }

    /**
     * Set level
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
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set modified
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
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set created
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
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
