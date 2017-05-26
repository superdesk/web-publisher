<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Bridge\Model\Event\Category;
use SWP\Component\Bridge\Model\Event\Date;
use SWP\Component\Bridge\Model\Event\Location;
use SWP\Component\Bridge\Model\Event\OccurStatus;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

class Event implements TimestampableInterface, EventInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $guid;

    /**
     * @var string
     */
    protected $etag;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $version;

    /**
     * @var string
     */
    protected $ingestId;

    /**
     * @var string
     */
    protected $recurrenceId;

    /**
     * @var string
     */
    protected $originalCreator;

    /**
     * @var string
     */
    protected $versionCreator;

    /**
     * @var string
     */
    protected $ingestProvider;

    /**
     * @var string
     */
    protected $originalSource;

    /**
     * @var string
     */
    protected $ingestProviderSequence;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $definitionShort;

    /**
     * @var string
     */
    protected $definitionLong;

    /**
     * @var Category[]
     */
    protected $anpaCategory = [];

    /**
     * @var Date
     */
    protected $dates;

    /**
     * @var Location[]
     */
    protected $locations = [];

    /**
     * @var OccurStatus
     */
    protected $occurStatus;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->anpaCategory = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * {@inheritdoc}
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getIngestId()
    {
        return $this->ingestId;
    }

    /**
     * {@inheritdoc}
     */
    public function setIngestId($ingestId)
    {
        $this->ingestId = $ingestId;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrenceId()
    {
        return $this->recurrenceId;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecurrenceId($recurrenceId)
    {
        $this->recurrenceId = $recurrenceId;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalCreator()
    {
        return $this->originalCreator;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalCreator($originalCreator)
    {
        $this->originalCreator = $originalCreator;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionCreator()
    {
        return $this->versionCreator;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersionCreator($versionCreator)
    {
        $this->versionCreator = $versionCreator;
    }

    /**
     * {@inheritdoc}
     */
    public function getIngestProvider()
    {
        return $this->ingestProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setIngestProvider($ingestProvider)
    {
        $this->ingestProvider = $ingestProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalSource()
    {
        return $this->originalSource;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalSource($originalSource)
    {
        $this->originalSource = $originalSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getIngestProviderSequence()
    {
        return $this->ingestProviderSequence;
    }

    /**
     * {@inheritdoc}
     */
    public function setIngestProviderSequence($ingestProviderSequence)
    {
        $this->ingestProviderSequence = $ingestProviderSequence;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionShort()
    {
        return $this->definitionShort;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefinitionShort($definitionShort)
    {
        $this->definitionShort = $definitionShort;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionLong()
    {
        return $this->definitionLong;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefinitionLong($definitionLong)
    {
        $this->definitionLong = $definitionLong;
    }

    /**
     * {@inheritdoc}
     */
    public function getAnpaCategory()
    {
        return $this->anpaCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setAnpaCategory($anpaCategory)
    {
        $this->anpaCategory = $anpaCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * {@inheritdoc}
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }

    /**
     * {@inheritdoc}
     */
    public function getOccurStatus()
    {
        return $this->occurStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setOccurStatus($occurStatus)
    {
        $this->occurStatus = $occurStatus;
    }
}
