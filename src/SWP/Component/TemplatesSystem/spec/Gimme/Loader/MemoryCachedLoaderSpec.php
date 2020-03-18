<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\TemplatesSystem\Gimme\Loader;

use PhpSpec\ObjectBehavior;
use SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaInterface;

class MemoryCachedLoaderSpec extends ObjectBehavior
{
    public function let(ArticleLoader $articleLoader, MetaInterface $meta)
    {
        $this->beConstructedWith($articleLoader);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\TemplatesSystem\Gimme\Loader\MemoryCachedLoader');
    }

    public function it_loads_this_same_data_only_once(ArticleLoader $articleLoader, MetaInterface $meta)
    {
        $articleLoader->load('article', [], [], 0)->willReturn($meta)->shouldBeCalledOnce();
        $this->beConstructedWith($articleLoader);

        $this->load('article')->shouldReturn($meta);
        $this->load('article')->shouldReturn($meta);
    }
}
