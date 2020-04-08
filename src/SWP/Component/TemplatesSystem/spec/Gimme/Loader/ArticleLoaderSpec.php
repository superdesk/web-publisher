<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\TemplatesSystem\Gimme\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class ArticleLoaderSpec extends ObjectBehavior
{
    public function let(MetaFactory $metaFactory, Meta $meta)
    {
        $metaFactory->create(Argument::type('array'), Argument::type('array'))->willReturn($meta);
        $this->beConstructedWith(__DIR__.'/../Meta', $metaFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader');
    }

    public function it_should_load_meta()
    {
        $this->load('article', [], LoaderInterface::SINGLE)->shouldBeAnInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta');
    }

    public function it_should_check_if_type_is_supported()
    {
        $this->isSupported('article')->shouldReturn(true);
        $this->isSupported('article2')->shouldReturn(false);
    }

    public function is_should_load_collection()
    {
        $this->load('article', [], LoaderInterface::COLLECTION)->shouldBeAnInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection');
    }
}
