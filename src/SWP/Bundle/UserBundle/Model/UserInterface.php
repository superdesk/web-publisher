<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface, PersistableInterface, TimestampableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getAbout(): string;

    public function setAbout(string $about);

    public function getFirstName(): string;

    public function setFirstName(string $firstName);

    public function getLastName(): string;

    public function setLastName(string $lastName);

    public function getExternalId(): string;

    public function setExternalId(string $externalId);
}
