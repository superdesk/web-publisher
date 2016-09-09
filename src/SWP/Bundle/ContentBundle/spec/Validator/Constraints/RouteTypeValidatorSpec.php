<?php

/**
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

use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Validator\Constraints\RouteType;
use SWP\Bundle\ContentBundle\Validator\Constraints\RouteTypeValidator;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @mixin RouteTypeValidator
 */
final class RouteTypeValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $context
    ) {
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteTypeValidator::class);
    }

    function it_extends_constraint_validator_class()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_returns_null_if_empty_value(RouteType $constraint)
    {
        $this->validate(null, $constraint)->shouldReturn(null);
    }

    function it_validates_value(
        RouteType $constraint,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ) {
        $constraint->message = 'The type "%type%" is not allowed. Supported types are: "%supportedTypes%".';

        $constraintViolationBuilder->setParameters(Argument::type('array'))->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $context->buildViolation(
            'The type "%type%" is not allowed. Supported types are: "%supportedTypes%".'
        )->willReturn($constraintViolationBuilder);

        $this->validate('fake', $constraint);
    }

    function it_does_nothing_when_value_is_valid(
        RouteType $constraint
    ) {
        $this->validate('content', $constraint);
        $this->validate('collection', $constraint);
    }
}
