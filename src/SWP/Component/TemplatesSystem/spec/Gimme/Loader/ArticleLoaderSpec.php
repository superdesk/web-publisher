<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\TemplatesSystem\Gimme\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArticleLoaderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__ . '/../Meta');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\TemplatesSystem\Gimme\Loader\ArticleLoader');
    }

    function it_should_load_meta()
    {
        $this->load('article', array(), \SWP\TemplatesSystem\Gimme\Loader\LoaderInterface::SINGLE)->shouldBeAnInstanceOf('SWP\TemplatesSystem\Gimme\Meta\Meta');
    }

    function it_should_check_if_type_is_supported()
    {
        $this->isSupported('article')->shouldReturn(true);
        $this->isSupported('article2')->shouldReturn(false);
    }
}
