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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadRoutesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const TEST_CACHE_TIME = 1;

    const TEST_CACHE_ROUTE_NAME = 'cache-route';

    const TEST_NO_CACHE_ROUTE_NAME = 'no-cache-route';

    /** @var array */
    protected $commonData = ['type' => 'content', 'content' => null];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $env = $this->getEnvironment();
        if ('test' === $env) {
            $this->loadRoute($manager, ['name' => self::TEST_NO_CACHE_ROUTE_NAME]);
            $this->loadRoute($manager, ['name' => self::TEST_CACHE_ROUTE_NAME, 'cacheTimeInSeconds' => self::TEST_CACHE_TIME]);
            $manager->flush();
        }
    }

    private function loadRoute($manager, $data)
    {
        $data = array_merge($data, $this->commonData);
        $route = $this->container->get('swp.factory.route')->create();
        $route->setName($data['name']);
        $route->setType($data['type']);
        $route->setContent($data['content']);
        if (isset($data['cacheTimeInSeconds'])) {
            $route->setCacheTimeInSeconds($data['cacheTimeInSeconds']);
        }

        $route = $this->container->get('swp.service.route')->fillRoute($route);
        $this->addReference('route_'.$data['name'], $route);

        $manager->persist($route);
    }

    public function getOrder(): int
    {
        return 3;
    }
}
