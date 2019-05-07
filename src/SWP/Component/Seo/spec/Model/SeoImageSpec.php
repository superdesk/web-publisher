<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Seo Component.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Seo\Model;

use SWP\Component\Seo\Model\SeoImage;
use SWP\Component\Seo\Model\SeoImageInterface;
use PhpSpec\ObjectBehavior;

class SeoImageSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SeoImage::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(SeoImageInterface::class);
    }
}
