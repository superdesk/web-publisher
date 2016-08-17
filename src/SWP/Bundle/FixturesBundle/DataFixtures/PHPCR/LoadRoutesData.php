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

    /** @var array */
    protected $commonData = ['type' => 'content', 'parent' => '/', 'content' => null];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();
        if ('test' === $env) {
            $parent = $manager->find(null, $this->getTenantPrefix().'/routes');
            $this->loadRoute($manager, ['name' => self::TEST_NO_CACHE_ROUTE_NAME], $parent);
            $this->loadRoute($manager, ['name' => self::TEST_CACHE_ROUTE_NAME, 'cacheTimeInSeconds' => self::TEST_CACHE_TIME], $parent);
            $manager->flush();
        }
    }

    private function loadRoute($manager, $data, $parent)
    {
        $data = array_merge($data, $this->commonData);
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
