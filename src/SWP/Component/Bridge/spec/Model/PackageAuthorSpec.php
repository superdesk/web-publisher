<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Bridge\Model;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Model\PackageAuthor;
use SWP\Component\Bridge\Model\PackageAuthorInterface;
use SWP\Component\Bridge\Model\PackageInterface;

final class PackageAuthorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PackageAuthor::class);
    }

    function it_should_implement_an_interface()
    {
        $this->shouldImplement(PackageAuthorInterface::class);
    }

    function it_should_have_package(PackageInterface $package)
    {
        $this->setPackage($package);
        $this->getPackage()->shouldReturn($package);
    }
}
