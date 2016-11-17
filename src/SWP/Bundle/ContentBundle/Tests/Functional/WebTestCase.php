<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected $container;

    protected $manager;

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'SWP\Bundle\ContentBundle\Tests\Functional\app\AppKernel';
    }

    protected function initDatabase()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->manager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($this->manager);
        $metadata = $this->manager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
