<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
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

class ChainLoaderSpec extends ObjectBehavior
{
    /**
     * @param \SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader $articleLoader
     * @param \SWP\Component\TemplatesSystem\Gimme\Meta\Meta            $meta
     */
    public function let($articleLoader, $meta)
    {
        $articleLoader->isSupported(Argument::exact('article'))->willReturn(true);
        $articleLoader->isSupported(Argument::exact('article2'))->willReturn(false);
        $articleLoader->load(Argument::exact('article'), Argument::type('array'), \SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface::SINGLE)->willReturn($meta);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\TemplatesSystem\Gimme\Loader\ChainLoader');
    }

    public function it_should_add_new_loader($articleLoader)
    {
        $this->addLoader($articleLoader);
    }

    public function it_should_load_meta($articleLoader, $meta)
    {
        $this->addLoader($articleLoader);
        $this->load('article', [])->shouldReturn($meta);
    }

    public function it_should_check_if_type_is_supported($articleLoader)
    {
        $this->addLoader($articleLoader);
        $this->isSupported('article')->shouldReturn(true);
        $this->isSupported('article2')->shouldReturn(false);
    }
}
