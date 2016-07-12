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
namespace spec\SWP\Component\Bridge;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\ChainValidatorInterface;
use SWP\Component\Bridge\Exception\RuntimeException;
use SWP\Component\Bridge\Validator\ValidatorInterface;
use SWP\Component\Bridge\ValidatorChain;

/**
 * @mixin ValidatorChain
 */
class ValidatorChainSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ValidatorChain::class);
    }

    public function it_implements_chain_validator_interface()
    {
        $this->shouldImplement(ChainValidatorInterface::class);
    }

    public function it_should_return_empty_array_of_validators()
    {
        $this->getValidators()->shouldReturn([]);
    }

    public function it_should_return_list_of_validators(ValidatorInterface $validator)
    {
        $this->addValidator($validator, 'alias');

        $validators = ['alias' => $validator];

        $this->getValidators()->shouldReturn($validators);
    }

    public function it_should_add_a_new_validator(ValidatorInterface $validator)
    {
        $this->addValidator($validator, 'alias');
        $this->getValidator('alias')->shouldReturn($validator);
    }

    public function it_should_throw_an_exception_when_adding_existing_validator(ValidatorInterface $validator)
    {
        $this->addValidator($validator, 'alias');

        $this->shouldThrow(RuntimeException::class)->duringAddValidator($validator, 'alias');
    }

    public function it_should_throw_an_exception_when_validator_doesnt_exist(ValidatorInterface $validator)
    {
        $this->addValidator($validator, 'alias');

        $this->shouldThrow(RuntimeException::class)->duringGetValidator('fake_alias');
    }
}
