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

namespace SWP\Bundle\SettingsBundle\Tests\Functional\Model;

use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements SettingsOwnerInterface, UserInterface
{
    protected $id;

    protected $password;

    protected $username;

    public function __construct($id, $username, $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public function getId()
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
        return true;
    }

    public function getRoles()
    {
        return ['ROLE_USER', 'ROLE_ADMIN'];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return '';
    }
}
