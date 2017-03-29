<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Manager;

use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER = 'user';

    /**
     * @return array
     */
    public function getScopes(): array;

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
    public function get(string $name, $scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.
     *
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    public function all($scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null);

    /**
     * Sets setting value by its name.
     *
     * @param string                      $name
     * @param mixed                       $value
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    public function set(string $name, $value, $scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null);

    /**
     * Clears setting value.
     *
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    public function clear(string $name, $scope = self::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null);
}
