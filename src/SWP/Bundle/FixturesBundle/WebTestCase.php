<?php

/**
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
namespace SWP\Bundle\FixturesBundle;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ClearableCache;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    /**
     * Initializes database.
     */
    protected function initDatabase()
    {
        $this->clearMetadataCache();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
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
}
