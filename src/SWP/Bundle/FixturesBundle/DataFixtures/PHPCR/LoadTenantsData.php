<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\CoreBundle\Document\Organization;
use SWP\Bundle\CoreBundle\Document\Tenant;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class LoadTenantsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();

        $organization1 = new Organization();
        $organization1->setName(OrganizationInterface::DEFAULT_NAME);
        $organization1->setCode('123456');
        $organization1->setParentDocument($manager->find(null, '/swp'));
        $manager->persist($organization1);

        $organization2 = new Organization();
        $organization2->setName('Organization2');
        $organization2->setCode('654321');
        $organization2->setParentDocument($manager->find(null, '/swp'));
        $manager->persist($organization2);
        $manager->flush();

        $tenant1 = new Tenant();
        $tenant1->setName('Default tenant');
        $tenant1->setSubdomain('default');
        $tenant1->setThemeName($env === 'test' ? 'swp/test-theme' : 'swp/default-theme');
        $tenant1->setCode('123abc');
        $tenant1->setOrganization($organization1);
        $manager->persist($tenant1);

        $tenant2 = new Tenant();
        $tenant2->setName('Client1 tenant');
        $tenant2->setSubdomain('client1');
        $tenant2->setThemeName($env === 'test' ? 'swp/test-theme' : 'swp/default-theme');
        $tenant2->setCode('456def');
        $tenant2->setOrganization($organization2);
        $manager->persist($tenant2);
        $manager->flush();

        $this->initBasePaths($env);
    }

    private function initBasePaths($env)
    {
        $kernel = $this->container->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:phpcr:repository:init',
            '--env' => $env,
        ));

        $application->run($input, new NullOutput());
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 0;
    }
}
