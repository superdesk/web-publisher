<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\WebRendererBundle\Doctrine\ODM\PHPCR\Tenant;
use SWP\Component\MultiTenancy\Model\SiteDocumentInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;

class LoadSitesData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tenants = [
            $manager->find(Tenant::class, '/swp/123456/123abc'),
            $manager->find(Tenant::class, '/swp/654321/456def'),
        ];

        $this->createHomepages($manager, $tenants);

        foreach ($tenants as $site) {
            if ($site instanceof TenantInterface) {
                $page = $manager->find(
                    'SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route',
                    $site->getId().'/routes/homepage'
                );

                $site->setHomepage($page);
            } else {
                throw new \RuntimeException(
                    sprintf('Unexpected child %s, %s expected.', get_class($site), TenantInterface::class)
                );
            }
        }

        $manager->flush();
    }

    private function createHomepages(ObjectManager $manager, array $tenants)
    {
        foreach ($tenants as $site) {
            if ($site instanceof TenantInterface) {
                $route = new Route();
                $route->setParentDocument($manager->find(null, $site->getId().'/routes'));
                $route->setName('homepage');
                $route->setType(RouteInterface::TYPE_CONTENT);

                $manager->persist($route);
            }
        }

        $manager->flush();
    }
}
