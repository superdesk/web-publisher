<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2021 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected static $container;

    protected $manager;

    public static function assertRedirect($response, $location)
    {
        self::assertTrue($response->isRedirect(), 'Response is not a redirect, got status code: '.$response->getStatusCode());
        self::assertEquals('http://localhost'.$location, $response->headers->get('Location'));
    }

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'SWP\Bundle\UserBundle\Tests\Functional\app\AppKernel';
    }

    protected function initDatabase()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        self::$container = $kernel->getContainer();
        $this->manager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($this->manager);
        $metadata = $this->manager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public static function createClient(array $options = [], array $server = [])
    {
        $server['PHP_AUTH_USER'] = 'publisher';
        $server['PHP_AUTH_PW'] = 'testpass';

        return parent::createClient($options, $server);
    }
}
