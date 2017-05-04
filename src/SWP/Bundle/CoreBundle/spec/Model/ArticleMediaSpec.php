<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\CoreBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\ArticleMedia as BaseArticleMedia;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\ArticleMediaInterface;

final class ArticleMediaSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleMedia::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement(ArticleMediaInterface::class);
    }

    function it_extends_article_media()
    {
        $this->shouldHaveType(BaseArticleMedia::class);
    }
}
