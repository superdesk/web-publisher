<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * @mixin Site
 */
class SiteSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Site::class);
        $this->shouldHaveType(\SWP\Bundle\MultiTenancyBundle\Document\Site::class);
    }

    public function it_should_implement_interfaces()
    {
        $this->shouldImplement(PersistableInterface::class);
    }
}
