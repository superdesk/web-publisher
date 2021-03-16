<?php

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

namespace spec\SWP\Bundle\CoreBundle\Theme\Asset;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Theme\Asset\PathResolver;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin PathResolver
 */
class PathResolverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PathResolver::class);
    }

    public function it_implements_path_resolver_interface()
    {
        $this->shouldImplement(PathResolverInterface::class);
    }

    public function it_returns_modified_path_if_its_referencing_asset(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');
        $this->resolve('theme/asset.min.js', '', $theme)->shouldReturn('bundles/_themes/theme/name/asset.min.js');
    }

    public function it_does_not_change_path_if_its_not_referencing_asset(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');
        $this->resolve('/path/asset.min.js', '', $theme)->shouldReturn('/path/asset.min.js');
    }
}
