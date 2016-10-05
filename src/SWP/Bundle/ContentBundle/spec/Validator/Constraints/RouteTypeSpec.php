<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Validator\Constraints;

use SWP\Bundle\ContentBundle\Validator\Constraints\RouteType;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Validator\Constraints\RouteTypeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @mixin RouteType
 */
final class RouteTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteType::class);
    }

    public function it_extends_constraint_class()
    {
        $this->shouldHaveType(Constraint::class);
    }

    public function it_has_a_message()
    {
        $this->message->shouldReturn('swp.route.type.route_type');
    }

    public function it_is_validated_by_route_type_validator()
    {
        $this->validatedBy()->shouldReturn(RouteTypeValidator::class);
    }
}
