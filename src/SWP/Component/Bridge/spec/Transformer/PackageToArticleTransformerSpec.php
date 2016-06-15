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
use SWP\Bundle\ContentBundle\Model\ArticleManagerInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\Bridge\Transformer\PackageToArticleTransformer;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * @mixin PackageToArticleTransformer
 */
class PackageToArticleTransformerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $routeRepository, ArticleManagerInterface $manager)
    {
        $this->beConstructedWith($routeRepository, $manager);
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
