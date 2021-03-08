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

namespace SWP\Bundle\UserBundle\Tests\Functional;

use SWP\Bundle\UserBundle\Tests\Functional\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        return new User(1, 'publisher', 'testpass');
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
