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

use SWP\Bundle\UserBundle\Util\CanonicalFieldsUpdater;
use SWP\Bundle\UserBundle\Util\PasswordUpdaterInterface;

abstract class UserManager implements UserManagerInterface
{
    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    public function __construct(
        PasswordUpdaterInterface $passwordUpdater,
        CanonicalFieldsUpdater $canonicalFieldsUpdater
    ) {
        $this->passwordUpdater = $passwordUpdater;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function createUser(): UserInterface
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(['emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(['usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username)]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(UserInterface $user)
    {
        $this->passwordUpdater->hashPassword($user);
    }

    /**
     * @return PasswordUpdaterInterface
     */
    protected function getPasswordUpdater()
    {
        return $this->passwordUpdater;
    }

    /**
     * @return CanonicalFieldsUpdater
     */
    protected function getCanonicalFieldsUpdater()
    {
        return $this->canonicalFieldsUpdater;
    }

    abstract public function deleteUser(UserInterface $user);

    abstract public function findUserBy(array $criteria);

    abstract public function findUsers();

    public function getClass()
    {
        return User::class;
    }

    abstract public function reloadUser(UserInterface $user);

    abstract public function updateUser(UserInterface $user, $andFlush = true);
}
