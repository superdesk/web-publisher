<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ClearableCache;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use SWP\Bundle\FixturesBundle\Registry\FixtureRegistry;

class WebTestCase extends BaseWebTestCase
{
    /**
     * Initializes database.
     */
    protected function initDatabase()
    {
        $this->clearMetadataCache();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:migrations:migrate', ['--force' => true, '--env' => 'test'], true);
    }

    /**
     * Clears metadata cache of the various cache drivers.
     */
    private function clearMetadataCache()
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var ClearableCache $cacheDriver */
        $cacheDriver = $entityManager->getConfiguration()->getMetadataCacheImpl();

        if (!$cacheDriver instanceof ArrayCache) {
            $cacheDriver->deleteAll();
        }
    }

    protected function tearDown()
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }

    protected function loadCustomFixtures(array $fixtures)
    {
        $env = $this->getContainer()->getParameter('test_env');

        $registry = new FixtureRegistry();
        $registry->setEnvironment($env);

        return $this->loadFixtures(
            $registry->getFixtures($fixtures),
            false,
            null,
            $env
        )->getReferenceRepository();
    }

    public static function createClient(array $options = [], array $server = [])
    {
        if (!array_key_exists('HTTP_Authorization', $server)) {
            $server['HTTP_Authorization'] = base64_encode('test_token:');
        }

        if (null === $server['HTTP_Authorization']) {
            unset($server['HTTP_Authorization']);
        }

        return parent::createClient($options, $server);
    }
}
