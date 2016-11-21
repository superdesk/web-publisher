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

namespace spec\SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\CoreBundle\Model\ContentList;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListInterface as BaseContentListInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

/**
 * @mixin ContentList
 */
final class ContentListSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentList::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(ContentListInterface::class);
        $this->shouldImplement(BaseContentListInterface::class);
        $this->shouldImplement(TenantAwareInterface::class);
    }

    public function it_has_no_tenant_code_by_default()
    {
        $this->getTenantCode()->shouldReturn(null);
    }

    public function its_tenant_code_is_mutable()
    {
        $this->setTenantCode('eyt645');
        $this->getTenantCode()->shouldReturn('eyt645');
    }
}
