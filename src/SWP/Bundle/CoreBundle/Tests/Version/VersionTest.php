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

namespace SWP\Bundle\CoreBundle\Tests\Version;

use SWP\Bundle\CoreBundle\Version\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $version = new Version();
        $version->setVersion('0.0.0')
            ->setCodeName('test')
            ->setReleaseDate('1970-01-01');

        $this->assertEquals('0.0.0', $version->getVersion());
        $this->assertEquals('test', $version->getCodeName());
        $this->assertEquals('1970-01-01', $version->getReleaseDate());

        unset($version);
    }
}
