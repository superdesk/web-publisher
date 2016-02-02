<?php

/**
 * This file is part of the Superdesk Web Publisher.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\WebRendererBundle\Version;

use PhpSpec\ObjectBehavior;

class VersionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\WebRendererBundle\Version\Version');
        $this->shouldImplement('SWP\UpdaterBundle\Version\VersionInterface');
    }

    public function it_should_return_version()
    {
        $this->setVersion('1.1.1')->shouldReturn($this);
        $this->getVersion()->shouldReturn('1.1.1');
    }
}
