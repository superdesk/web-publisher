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
namespace spec\SWP\Component\Bridge\Transformer;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Transformer\DataTransformerChain;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;

/**
 * @mixin DataTransformerChain
 */
class DataTransformerChainSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DataTransformerChain::class);
    }

    public function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_should_return_empty_array_of_validators()
    {
        $this->getTransformers()->shouldReturn([]);
    }

    public function it_should_return_an_array_of_transformers(DataTransformerInterface $transformer)
    {
        $this->beConstructedWith([$transformer]);

        $this->getTransformers()->shouldReturn([$transformer]);
    }

    public function it_should_transform_value_by_many_transformers(
        DataTransformerInterface $firstTransformer,
        DataTransformerInterface $secondTransformer
    ) {
        $value = 'foo';
        $transformedValue = 'bar';
        $firstTransformer->transform($value)->willReturn($transformedValue);
        $secondTransformer->transform($transformedValue)->willReturn('baz');
        $this->beConstructedWith([$firstTransformer, $secondTransformer]);

        $this->transform($value)->shouldReturn('baz');
    }

    public function it_should_reverse_transform_value(
        DataTransformerInterface $firstTransformer,
        DataTransformerInterface $secondTransformer
    ) {
        $value = 'foo';
        $secondTransformer->reverseTransform($value)->willReturn('bar');
        $firstTransformer->reverseTransform('bar')->willReturn('baz');
        $this->beConstructedWith([$firstTransformer, $secondTransformer]);

        $this->reverseTransform($value)->shouldReturn('baz');
    }
}
