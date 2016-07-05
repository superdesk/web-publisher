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
use Prophecy\Argument;
use SWP\Component\Bridge\Exception\MethodNotSupportedException;
use SWP\Component\Bridge\Exception\TransformationFailedException;
use SWP\Component\Bridge\Model\Package;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;
use SWP\Component\Bridge\Validator\ValidatorInterface;
use SWP\Component\Common\Serializer\SerializerInterface;

/**
 * @mixin JsonToPackageTransformer
 */
class JsonToPackageTransformerSpec extends ObjectBehavior
{
    function let(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->beConstructedWith($serializer, $validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JsonToPackageTransformer::class);
    }

    function it_implements_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_should_transform_json_to_package(
        PackageInterface $package,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $json = '{valid json}';
        $package->getHeadline()->willReturn('headline');
        $package->getSlugline()->willReturn('slug');
        $package->getLanguage()->willReturn('en');

        $validator->isValid($json)->willReturn(true);
        $serializer->deserialize($json, Argument::exact(Package::class), Argument::exact('json'))->willReturn($package);

        $this->transform($json)->shouldReturn($package);
    }

    function it_should_throw_exception(ValidatorInterface $validator)
    {
        $validator->isValid('{invalid json}')->willReturn(false);

        $this
            ->shouldThrow(TransformationFailedException::class)
            ->during('transform', ['{invalid json}']);
    }

    function it_should_not_support_reverse_transform()
    {
        $this
            ->shouldThrow(MethodNotSupportedException::class)
            ->during('reverseTransform', [new \stdClass()]);
    }
}
