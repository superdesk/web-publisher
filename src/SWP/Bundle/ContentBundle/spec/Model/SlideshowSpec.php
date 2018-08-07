<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Model;

use SWP\Bundle\ContentBundle\Model\Slideshow;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;

final class SlideshowSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Slideshow::class);
    }

    function it_should_implement_article_interface()
    {
        $this->shouldImplement(SlideshowInterface::class);
    }
}
