<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\Bridge\Validator;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Validator\JsonValidator;
use SWP\Component\Bridge\Validator\NinjsValidator;
use SWP\Component\Bridge\Validator\ValidatorInterface;
use SWP\Component\Bridge\Validator\ValidatorOptionsInterface;

/**
 * @mixin NinjsValidator
 */
class NinjsValidatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(NinjsValidator::class);
        $this->shouldHaveType(JsonValidator::class);
    }

    public function it_implements_validator_interfaces()
    {
        $this->shouldImplement(ValidatorInterface::class);
        $this->shouldImplement(ValidatorOptionsInterface::class);
    }

    public function its_isValid_method_should_return_false()
    {
        $this->isValid('fake example content')->shouldReturn(false);
    }

    public function its_isValid_method_should_return_true()
    {
        $this->isValid(file_get_contents(__DIR__.'/../ninjs.json'))->shouldReturn(true);
    }

    public function it_has_a_format()
    {
        $this->getFormat()->shouldReturn('ninjs');
    }
}
