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

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\CoreBundle\EventListener\TenantRevisionListener;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\Revision\Manager\RevisionManagerInterface;

class TenantRevisionListenerTest extends WebTestCase
{
    /**
     * @var RevisionManagerInterface
     */
    protected $manager;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'container', 'container_widget']);
        $this->manager = $this->getContainer()->get('swp.manager.revision');
    }

    public function testInitialize()
    {
        $listener = new TenantRevisionListener(
            $this->getContainer()->get('swp.repository.revision'),
            $this->getContainer()->get('swp_revision.context.revision')
        );

        self::assertInstanceOf(TenantRevisionListener::class, $listener);
    }
}
