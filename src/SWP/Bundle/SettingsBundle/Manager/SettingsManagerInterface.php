<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Manager;

use SWP\Bundle\SettingsBundle\Context\ScopeContext;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    /**
     * Returns setting value by its name.
     *
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     * @param null                        $default
     *
     * @return mixed
     */
    public function get(string $name, $scope = ScopeContext::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.
     *
     * @return array
     */
    public function all();

    /**
     * Sets setting value by its name.
     *
     * @param string                      $name
     * @param mixed                       $value
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return SettingsInterface
     */
    public function set(string $name, $value, $scope = ScopeContext::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null);

    /**
     * Clears setting value.
     *
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    public function clear(string $name, $scope = ScopeContext::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null);

    /**
     * @param $scope
     */
    public function clearAllByScope(string $scope = ScopeContextInterface::SCOPE_GLOBAL): void;

    /**
     * @param string $scope
     *
     * @return array
     */
    public function getAllByScope(string $scope): array;
}
