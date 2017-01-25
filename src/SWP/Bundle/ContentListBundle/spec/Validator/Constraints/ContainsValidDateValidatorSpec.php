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
use SWP\Bundle\ContentListBundle\Validator\Constraints\ContainsValidDateValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ContainsValidDateValidatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ContainsValidDateValidator::class);
    }

    public function it_is_a_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_a_value(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ) {
        $this->initialize($executionContext);

        $constraint = new ContainsValidDate();
        $executionContext->buildViolation($constraint->message)->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->setParameter('%value%', 'publishedAt')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->setParameter('%date%', '2017-00-01')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $constraintViolationBuilder
            ->setParameter('%value%', 'publishedBefore')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->setParameter('%date%', '2017-00-01')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $constraintViolationBuilder
            ->setParameter('%value%', 'publishedAfter')->shouldNotBeCalled();

        $value = [
            'publishedAt' => '2017-00-01',
            'publishedBefore' => '2017-00-01',
            'publishedAfter' => '2017-10-01',
        ];

        $this->validate($value, $constraint);
    }

    function it_validates_a_value_without_violations(
        ExecutionContextInterface $executionContext
    ) {
        $this->initialize($executionContext);
        $constraint = new ContainsValidDate();
        $executionContext->buildViolation($constraint->message)->shouldNotBeCalled();

        $value = [
            'publishedAt' => '2017-01-01',
        ];

        $this->validate($value, $constraint);
    }
}
