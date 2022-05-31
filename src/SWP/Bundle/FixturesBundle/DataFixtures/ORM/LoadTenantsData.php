<?php

declare(strict_types=1);

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

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadTenantsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $env = $this->getEnvironment();

        $this->loadFixtures(
            [
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/organization.yml',
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/tenant.yml',
            ]
        );

        $manager->flush();

        $tenantContext = $this->container->get('swp_multi_tenancy.tenant_context');
        if (null === $tenantContext->getTenant()) {
            $tenantContext->setTenant(
                $this->container->get('swp.repository.tenant')->findOneByCode('123abc')
            );
        }
    }

    public function getOrder(): int
    {
        return -1;
    }
}
