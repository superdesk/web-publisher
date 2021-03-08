<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Tests\Functional\Model;

use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends \SWP\Bundle\UserBundle\Model\User implements UserInterface, PersistableInterface
{
    protected $id;

    protected $password;

    protected $username;

    public function __construct($id = null, $username = null, $password = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
        return true;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER', 'ROLE_ADMIN'];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getSalt()
    {
        return '';
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
