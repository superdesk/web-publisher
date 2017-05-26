<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Interface EventInterface.
 */
interface EventInterface extends PersistableInterface
{
    /**
     * @return string
     */
    public function getGuid();

    /**
     * @param string $guid
     */
    public function setGuid($guid);

    /**
     * @return int
     */
    public function getVersion();

    /**
     * @param int $version
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getIngestId();

    /**
     * @param string $ingestId
     */
    public function setIngestId($ingestId);

    /**
     * @return string
     */
    public function getRecurrenceId();

    /**
     * @param string $recurrenceId
     */
    public function setRecurrenceId($recurrenceId);

    /**
     * @return string
     */
    public function getOriginalCreator();

    /**
     * @param string $originalCreator
     */
    public function setOriginalCreator($originalCreator);

    /**
     * @return string
     */
    public function getVersionCreator();

    /**
     * @param string $versionCreator
     */
    public function setVersionCreator($versionCreator);

    /**
     * @return string
     */
    public function getIngestProvider();

    /**
     * @param string $ingestProvider
     */
    public function setIngestProvider($ingestProvider);

    /**
     * @return string
     */
    public function getOriginalSource();

    /**
     * @param string $originalSource
     */
    public function setOriginalSource($originalSource);

    /**
     * @return string
     */
    public function getIngestProviderSequence();

    /**
     * @param string $ingestProviderSequence
     */
    public function setIngestProviderSequence($ingestProviderSequence);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDefinitionShort();

    /**
     * @param string $definitionShort
     */
    public function setDefinitionShort($definitionShort);

    /**
     * @return string
     */
    public function getDefinitionLong();

    /**
     * @param string $definitionLong
     */
    public function setDefinitionLong($definitionLong);

    /**
     * @return ArrayCollection
     */
    public function getAnpaCategory();

    /**
     * @param ArrayCollection $anpaCategory
     */
    public function setAnpaCategory($anpaCategory);

    /**
     * @return Date
     */
    public function getDates();

    /**
     * @param Date $dates
     */
    public function setDates($dates);

    /**
     * @return ArrayCollection
     */
    public function getLocations();

    /**
     * @param ArrayCollection $locations
     */
    public function setLocations($locations);

    /**
     * @return OccurStatus
     */
    public function getOccurStatus();

    /**
     * @param OccurStatus $occurStatus
     */
    public function setOccurStatus($occurStatus);
}
