<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentListBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentListBundle\Validator\Constraints\ContainsValidDate;
use Symfony\Component\Validator\Constraint;

final class ContainsValidDateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContainsValidDate::class);
    }

    function it_is_a_constraint()
    {
        $this->shouldHaveType(Constraint::class);
    }
}
