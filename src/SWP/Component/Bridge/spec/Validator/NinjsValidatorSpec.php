<?php

namespace spec\SWP\Component\Bridge\Validator;

use JsonSchema\Validator;
use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Validator\JsonValidator;
use SWP\Component\Bridge\Validator\NinjsValidator;
use SWP\Component\Bridge\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin NinjsValidator
 */
class NinjsValidatorSpec extends ObjectBehavior
{
    function let(Validator $validator)
    {
        $this->beConstructedWith($validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NinjsValidator::class);
        $this->shouldHaveType(JsonValidator::class);
    }

    function it_implements_theme_factory_interface()
    {
        $this->shouldImplement(ValidatorInterface::class);
    }

    function its_isValid_method_should_return_false(Request $request)
    {
        $request->getContent()->willReturn('fake example content');

        $this->isValid($request)->shouldReturn(false);
    }

    function its_isValid_method_should_return_true(Request $request, Validator $validator)
    {
        $request->getContent()->willReturn('valid json content');
        $this->setSchema('example schema');
        $validator->check('valid json content', 'example schema')->shouldBeCalled();
        $validator->isValid()->willReturn(true);

        $this->isValid($request)->shouldReturn(true);
    }

    function it_has_a_format()
    {
        $this->getFormat()->shouldReturn('ninjs');
    }
}
