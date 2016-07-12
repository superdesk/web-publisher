<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\WebRendererBundle\Theme\Helper;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Theme\Helper\ThemeHelper;
use SWP\Bundle\WebRendererBundle\Theme\Helper\ThemeHelperInterface;

/**
 * @mixin ThemeHelper
 */
class ThemeHelperSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeHelper::class);
    }

    public function it_implements_theme_helper_interface()
    {
        $this->shouldImplement(ThemeHelperInterface::class);
    }

    public function it_returns_empty_array()
    {
        $this->process()->shouldReturn([]);
    }

    public function it_processes_theme_config()
    {
        $this->beConstructedWith(['/path/to/theme/']);

        $this->process([
            'path' => '/path/to/theme/default',
            'name' => 'swp/theme-name',
        ])->shouldReturn([
            'path' => '/path/to/theme/default',
            'name' => 'swp/theme-name@default',
        ]);
    }
}
