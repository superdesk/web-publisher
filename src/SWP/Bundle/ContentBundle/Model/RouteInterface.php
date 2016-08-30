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
namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface RouteInterface extends PersistableInterface
{
    const TYPE_CONTENT = 'content';
    const TYPE_COLLECTION = 'collection';

    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @param string $templateName
     */
    public function setTemplateName($templateName);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * Sets the variable pattern - the variable part of the url pattern.
     *
     * @param string $pattern
     */
    public function setVariablePattern($pattern);

    /**
     * Gets variable pattern - the variable part of the url pattern.
     *
     * @return string
     */
    public function getVariablePattern();

    /**
     * Sets requirements for route.
     *
     * @param array $requirements
     */
    public function setRequirements(array $requirements);

    /**
     * Gets requirements for route.
     *
     * @return array
     */
    public function getRequirements();

    /**
     * Sets a requirement for the given key.
     *
     * @param string $key   The key
     * @param string $regex The regex
     */
    public function setRequirement($key, $regex);

    /**
     * Gets a requirement for the given key.
     *
     * @param string $key The key
     *
     * @return string|null The regex or null when not given
     */
    public function getRequirement($key);

    /**
     * @param string $name
     *
     * @return string
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getCacheTimeInSeconds();

    /**
     * @param int $cacheTimeInSeconds
     */
    public function setCacheTimeInSeconds($cacheTimeInSeconds);

    /**
     * @return array
     */
    public function getDefaults();

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults);

    /**
     * @param array $defaults
     */
    public function addDefaults(array $defaults);

    /**
     * @param string $name
     * @param string $value
     */
    public function setDefault($name, $value);
}
