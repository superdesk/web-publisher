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
namespace spec\SWP\Bundle\ContentBundle\Transformer;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Transformer\PackageToArticleTransformer;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;

/**
 * @mixin PackageToArticleTransformer
 */
class PackageToArticleTransformerSpec extends ObjectBehavior
{
    function let(ArticleFactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PackageToArticleTransformer::class);
    }

    function it_implements_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }
}
