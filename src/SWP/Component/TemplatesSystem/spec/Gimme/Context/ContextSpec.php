<?php

/**
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

namespace spec\SWP\Component\TemplatesSystem\Gimme\Context;

use PhpSpec\ObjectBehavior;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use Doctrine\Common\Cache\Cache;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * @mixin Context
 */
class ContextSpec extends ObjectBehavior
{
    public function let(Cache $cache, Meta $meta)
    {
        $meta->getConfiguration()->willReturn(['name' => 'article']);

        $this->beConstructedWith($cache, __DIR__.'/../Meta/Resources/meta');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }

    /**
     * @param \SWP\Component\TemplatesSystem\Gimme\Meta\Meta $meta
     */
    public function it_shuld_register_new_meta($meta)
    {
        $this->registerMeta($meta)->shouldReturn(true);
    }

    public function it_should_set_new_meta($meta)
    {
        $this->registerMeta($meta)->shouldReturn(true);
    }

    public function it_should_read_meata($meta)
    {
        $this->registerMeta($meta)->shouldReturn(true);
        $this->article = $meta;
        $this->article->shouldReturn($meta);
    }

    public function it_should_save_and_read_current_page_info()
    {
        $currentPage = [
            'id' => 1,
            'name' => 'About',
            'type' => 1,
            'slug' => 'about-us',
            'templateName' => 'static.html.twig',
            'externalUrl' => null,
            'contentPath' => '/content/about-us',
            'articles' => null,
            'route_name' => 'swp_page_about',
        ];

        $this->setCurrentPage($currentPage)->shouldReturn($this);
        $this->getCurrentPage()->shouldReturn($currentPage);
    }
}
