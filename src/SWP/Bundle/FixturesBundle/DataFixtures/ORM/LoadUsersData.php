<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadUsersData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var UserInterface $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('test.user');
        $user->setEmail('test.user@sourcefabric.org');
        $user->setPassword('testPassword');

        $userManager->updateUser($user);

        $tokenValidDate = new \DateTime();
        $tokenValidDate->modify('+48 hours');
        $apiKey = $this->container->get('swp.factory.api_key')->create();
        $apiKey->setApiKey('test_token');
        $apiKey->setUser($user);
        $apiKey->setValidTo($tokenValidDate);
        $this->container->get('swp.repository.api_key')->add($apiKey);
    }
}
