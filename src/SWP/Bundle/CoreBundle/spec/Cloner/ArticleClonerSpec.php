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
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Cloner;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Cloner\ArticleCloner;
use SWP\Bundle\CoreBundle\Cloner\ArticleClonerInterface;
use SWP\Bundle\CoreBundle\Factory\ClonerFactoryInterface;

class ArticleClonerSpec extends ObjectBehavior
{
    public function let(ClonerFactoryInterface $clonerFactory)
    {
        $this->beConstructedWith($clonerFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleCloner::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(ArticleClonerInterface::class);
    }
}
