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

use SWP\Component\Seo\Model\SeoMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Component\Seo\Model\SeoMetadataInterface;

class SeoMetadataSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SeoMetadata::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(SeoMetadataInterface::class);
    }
}
