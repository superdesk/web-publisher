<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\ApiKey;
use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;

final class ApiKeySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ApiKey::class);
    }

    function it_implements_interface()
    {
        $this->shouldHaveType(ApiKeyInterface::class);
    }
}
