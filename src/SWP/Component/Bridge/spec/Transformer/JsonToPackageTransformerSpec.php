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
    public function let(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->beConstructedWith($serializer, $validator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(JsonToPackageTransformer::class);
    }

    public function it_implements_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_should_transform_json_to_package(
        PackageInterface $package,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $json = '{
            "language": "en",
            "slugline": "slugline",
            "headline": "headline"
        }';

        $package->getHeadline()->willReturn('headline');
        $package->getSlugline()->willReturn('slugline');
        $package->getLanguage()->willReturn('en');

        $validator->isValid($json)->willReturn(true);
        $serializer->deserialize($json, Argument::exact(Package::class), Argument::exact('json'))->willReturn($package);

        $this->transform($json)->shouldReturn($package);
    }

    public function it_should_throw_exception(ValidatorInterface $validator)
    {
        $validator->isValid('{invalid json}')->willReturn(false);

        $this
            ->shouldThrow(TransformationFailedException::class)
            ->during('transform', ['{invalid json}']);
    }

    public function it_should_not_support_reverse_transform()
    {
        $this
            ->shouldThrow(MethodNotSupportedException::class)
            ->during('reverseTransform', [new \stdClass()]);
    }
}
