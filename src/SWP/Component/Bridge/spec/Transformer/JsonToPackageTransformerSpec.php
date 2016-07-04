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
}
