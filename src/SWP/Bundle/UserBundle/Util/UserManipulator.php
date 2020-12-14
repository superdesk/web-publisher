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

namespace SWP\Bundle\UserBundle\Util;

use SWP\Bundle\UserBundle\Event\UserEvent;
use SWP\Bundle\UserBundle\Model\UserInterface;
use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use SWP\Bundle\UserBundle\SWPUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Executes some manipulations on the users.
 *
 */
class UserManipulator
{
    /**
     * User manager.
     *
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * UserManipulator constructor.
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $dispatcher, RequestStack $requestStack)
    {
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param bool   $active
     * @param bool   $superadmin
     *
     * @return \SWP\Bundle\UserBundle\Model\UserInterface
     */
    public function create($username, $password, $email, $active, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled((bool) $active);
        $user->setSuperAdmin((bool) $superadmin);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, SWPUserEvents::USER_CREATED);

        return $user;
    }

    /**
     * Activates the given user.
     *
     * @param string $username
     */
    public function activate($username)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setEnabled(true);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, SWPUserEvents::USER_ACTIVATED);
    }

    /**
     * Deactivates the given user.
     *
     * @param string $username
     */
    public function deactivate($username)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setEnabled(false);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, SWPUserEvents::USER_DEACTIVATED);
    }

    /**
     * Changes the password for the given user.
     *
     * @param string $username
     * @param string $password
     */
    public function changePassword($username, $password)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setPlainPassword($password);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, SWPUserEvents::USER_PASSWORD_CHANGED);
    }

    /**
     * Promotes the given user.
     *
     * @param string $username
     */
    public function promote($username)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setSuperAdmin(true);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, SWPUserEvents::USER_PROMOTED);
    }

    /**
     * Demotes the given user.
     *
     * @param string $username
     */
    public function demote($username)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        $user->setSuperAdmin(false);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->getRequest());
        $this->dispatcher->dispatch($event, SWPUserEvents::USER_DEMOTED);
    }

    /**
     * Adds role to the given user.
     *
     * @param string $username
     * @param string $role
     *
     * @return bool true if role was added, false if user already had the role
     */
    public function addRole($username, $role)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        if ($user->hasRole($role)) {
            return false;
        }
        $user->addRole($role);
        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * Removes role from the given user.
     *
     * @param string $username
     * @param string $role
     *
     * @return bool true if role was removed, false if user didn't have the role
     */
    public function removeRole($username, $role)
    {
        $user = $this->findUserByUsernameOrThrowException($username);
        if (!$user->hasRole($role)) {
            return false;
        }
        $user->removeRole($role);
        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * Finds a user by his username and throws an exception if we can't find it.
     *
     * @param string $username
     *
     * @throws \InvalidArgumentException When user does not exist
     *
     * @return UserInterface
     */
    private function findUserByUsernameOrThrowException($username)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        return $user;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
