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

namespace spec\SWP\Component\TemplatesSystem\Gimme\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class ContextSpec extends ObjectBehavior
{
    /**
     * @param \SWP\Component\TemplatesSystem\Gimme\Meta\Meta $meta
     */
    function let($meta)
    {}

    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\TemplatesSystem\Gimme\Context\Context');
    }

    function it_shuld_register_new_meta($meta)
    {
        $this->registerMeta('item', $meta)->shouldReturn(true);
    }

    function it_should_set_new_meta($meta)
    {
        $this->registerMeta('item', $meta)->shouldReturn(true);
        $this->item = $meta;
    }

    function it_should_read_meata($meta)
    {
        $this->registerMeta('item', $meta)->shouldReturn(true);
        $this->item = $meta;
        $this->item->shouldReturn($meta);
    }

    function it_should_save_and_read_current_page_info()
    {
        $currentPage = [
            "id" => 1,
            "name" => "About",
            "type" => 1,
            "slug" => "about-us",
            "templateName" => "static.html.twig",
            "externalUrl" => null,
            "contentPath" => "/content/about-us",
            "articles" => null,
            "route_name" => "swp_page_about",
        ];

        $this->setCurrentPage($currentPage)->shouldReturn($this);
        $this->getCurrentPage()->shouldReturn($currentPage);
    }
}
