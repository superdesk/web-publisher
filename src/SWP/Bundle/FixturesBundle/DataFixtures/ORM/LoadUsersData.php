<?php

declare(strict_types=1);

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
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadUsersData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $userManager = $this->container->get('swp_user.user_manager');

        /** @var UserInterface $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('test.user');
        $user->setEmail('test.user@sourcefabric.org');
        
        $passwordEncoder = $this->container->get('security.password_encoder');          

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                'testPassword'
            )
        );

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                'testPassword'
            )
        );
//        $user->setExternalId('1');
        $user->addRole('ROLE_INTERNAL_API');

        $userManager->updateUser($user);

        $apiKey = $this->container->get('swp.factory.api_key')->create($user, base64_encode('test_token:'));
        $this->container->get('swp.repository.api_key')->add($apiKey);

        /** @var UserInterface $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('test.client1');
        $user->setEmail('test.client1@sourcefabric.org');
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                'testPassword'
            )
        );
        $user->addRole('ROLE_INTERNAL_API');
//        $user->setExternalId('2');
        $user->setOrganization($this->container->get('swp.repository.organization')->findOneByCode('654321'));

        $userManager->updateUser($user);

        $apiKey = $this->container->get('swp.factory.api_key')->create($user, base64_encode('client1_token'));
        $this->container->get('swp.repository.api_key')->add($apiKey);

        /** @var UserInterface $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('test.client2');
        $user->setEmail('test.client2@sourcefabric.org');
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                'testPassword'
            )
        );
        $user->addRole('ROLE_INTERNAL_API');
//        $user->setExternalId('3');
        $user->setOrganization($this->container->get('swp.repository.organization')->findOneByCode('123456'));

        $userManager->updateUser($user);

        $apiKey = $this->container->get('swp.factory.api_key')->create($user, base64_encode('client2_token'));
        $this->container->get('swp.repository.api_key')->add($apiKey);
    }

    public function getOrder(): int
    {
        return 0;
    }
}
