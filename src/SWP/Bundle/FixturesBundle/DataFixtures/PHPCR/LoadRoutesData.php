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
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;

class LoadRoutesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const TEST_CACHE_TIME = 1;
    const TEST_CACHE_ROUTE_NAME = 'cache-route';
    const TEST_NO_CACHE_ROUTE_NAME = 'no-cache-route';

    const DEV_ROUTES = ['politics', 'business', 'scitech', 'health', 'entertainment', 'sports', 'dialogue'];

    /** @var array */
    protected $commonTestData = ['type' => 'collection', 'parent' => '/', 'content' => null];

    /** @var array */
    protected $commonDevData = ['type' => 'collection', 'content' => null];


    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $parent = $manager->find(null, $this->getTenantPrefix().'/routes');

        $env = $this->getEnvironment();
        if ('test' === $env) {
            $this->loadRoute($manager, ['name' => self::TEST_NO_CACHE_ROUTE_NAME], $this->commonTestData, $parent);
            $this->loadRoute($manager, ['name' => self::TEST_CACHE_ROUTE_NAME, 'cacheTimeInSeconds' => self::TEST_CACHE_TIME], $this->commonTestData, $parent);
            $manager->flush();
        } else {
            foreach (self::DEV_ROUTES as $devRoute) {
                $this->loadRoute($manager, ['name' => $devRoute], $this->commonDevData, $parent);
            }
            $manager->flush();
        }
    }

    private function loadRoute($manager, $data, $commonData, $parent)
    {
        $data = array_merge($data, $commonData);
        $route = new Route();
        $route->setParentDocument($parent);
        $route->setName($data['name']);
        $route->setType($data['type']);
        $route->setContent($data['content']);
        if (isset($data['cacheTimeInSeconds'])) {
            $route->setCacheTimeInSeconds($data['cacheTimeInSeconds']);
        }

        $manager->persist($route);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
