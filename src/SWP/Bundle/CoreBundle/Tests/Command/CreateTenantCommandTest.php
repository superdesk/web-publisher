<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Command;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\Revision\Model\RevisionInterface;

class CreateTenantCommandTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
    }

    public function testCommand()
    {
        $result = $this->runCommand('swp:tenant:create', ['organization code' => '123456', 'subdomain' => 'test23', 'domain' => 'localhost', 'name' => 'Revision Aware Tenant'], true);
        preg_match_all('/code: \\e\[32m([azAZ\w]+)\\e\[39m/', $result, $matches);
        $code = $matches[1][0];
        $revisionRepository = $this->getContainer()->get('swp.repository.revision');
        self::assertInstanceOf(
            RevisionInterface::class,
            $revisionRepository
                ->getPublishedRevision()
                ->andWhere('r.tenantCode = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->getOneOrNullResult()
        );

        self::assertInstanceOf(
            RevisionInterface::class,
            $revisionRepository
                ->getWorkingRevision()
                ->andWhere('r.tenantCode = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }
}
