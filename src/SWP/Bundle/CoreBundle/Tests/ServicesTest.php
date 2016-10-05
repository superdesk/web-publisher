<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;

class ServicesTest extends WebTestCase
{
    public function testContainerServices()
    {
        $client = static::createClient();
        foreach ($client->getContainer()->getServiceIds() as $serviceId) {
            if (strpos($serviceId, 'swp_') !== false) {
                try {
                    $startedAt = microtime(true);
                    $client->getContainer()->get($serviceId);
                    $elapsed = (microtime(true) - $startedAt) * 1000;

                    if ($elapsed > 100) {
                        $this->markTestSkipped(
                            sprintf('Slow service id %s', $serviceId)
                        );
                    }
                } catch (InactiveScopeException $e) {
                }
            }
        }
    }
}
