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

/**
 * Interface to be implemented by user managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to users should happen through this interface.
 *
 * The class also contains ACL annotations which will only work if you have the
 * SecurityExtraBundle installed, otherwise they will simply be ignored.
 */
interface UserManagerInterface
{
    /**
     * Creates an empty user instance.
     */
    public function createUser(): UserInterface;

    /**
     * Deletes a user.
     */
    public function deleteUser(UserInterface $user);

    /**
     * Finds one user by the given criteria.
     *
     * @return UserInterface|null
     */
    public function findUserBy(array $criteria);

    /**
     * Find a user by its username.
     *
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findUserByUsername($username);

    /**
     * Finds a user by its email.
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findUserByEmail($email);

    /**
     * Finds a user by its username or email.
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface|null
     */
    public function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string $token
     *
     * @return UserInterface|null
     */
    public function findUserByConfirmationToken($token);

    /**
     * Returns a collection with all user instances.
     *
     * @return \Traversable
     */
    public function findUsers();

    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a user.
     *
     * @param bool $andFlush
     */
    public function updateUser(UserInterface $user, $andFlush = true);

    public function find(int $id);
}
