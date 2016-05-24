<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\WebRendererBundle\Twig;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Twig\WebRendererExtension;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Twig_Extension;

class WebRendererExtensionSpec extends ObjectBehavior
{
    function let(ThemeContextInterface $themeContext)
    {
        $this->beConstructedWith($themeContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WebRendererExtension::class);
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType(Twig_Extension::class);
    }

    function it_should_return_global_variables(ThemeInterface $theme, ThemeContextInterface $themeContext)
    {
        $theme->getName()->willReturn('swp/theme-one');
        $theme->getPath()->willReturn('/path/to/theme/');

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);

        $globals = [
            'theme' => $theme,
        ];

        $this->getGlobals()->shouldReturn($globals);
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('swp_webrenderer');
    }
}
