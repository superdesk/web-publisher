<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends PersistableInterface, TimestampableInterface, BaseUserInterface, EquatableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USR';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getEmail();

    /**
     * @return string
     */
    public function getAbout();

    public function setAbout(string $about);

    /**
     * @return string
     */
    public function getFirstName();

    public function setFirstName(string $firstName);

    /**
     * @return string
     */
    public function getLastName();

    public function setLastName(string $lastName);

    /**
     * @return string
     */
    public function getExternalId();

    public function setExternalId(string $externalId);

    public function setPassword(string $password);

    public function addRole(string $role);

    public function removeRole(string $role);
}
