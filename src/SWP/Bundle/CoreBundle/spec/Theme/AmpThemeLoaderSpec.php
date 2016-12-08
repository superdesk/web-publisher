<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Theme;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Theme\AmpThemeLoader;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Takeit\Bundle\AmpHtmlBundle\Loader\ThemeLoaderInterface;

/**
 * @mixin AmpThemeLoader
 */
final class AmpThemeLoaderSpec extends ObjectBehavior
{
    public function let(\Twig_Loader_Filesystem $filesystem, ThemeContextInterface $themeContext)
    {
        $this->beConstructedWith($filesystem, $themeContext, 'amp/amp-theme');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AmpThemeLoader::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(ThemeLoaderInterface::class);
    }

    public function it_loads_amp_theme(
        \Twig_Loader_Filesystem $filesystem,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getPath()->willReturn('/path/to/theme');
        $themeContext->getTheme()->willReturn($theme);
        $filesystem->addPath('/path/to/theme/amp/amp-theme', 'amp_theme')->shouldBeCalled();

        $this->load();
    }
}
