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

use Doctrine\ODM\PHPCR\HierarchyInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\RouteObjectInterface;
use SWP\Bundle\MultiTenancyBundle\Document\Page;

/**
 * @mixin Route
 */
class RouteSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Route::class);
        $this->shouldHaveType(Page::class);
    }

    public function it_should_implement_interfaces()
    {
        $this->shouldImplement(RouteObjectInterface::class);
        $this->shouldImplement(HierarchyInterface::class);
    }

    public function it_has_no_template_by_default()
    {
        $this->getTemplateName()->shouldReturn(null);
    }

    public function its_template_is_mutable()
    {
        $this->setTemplateName('index.html.twig');
        $this->getTemplateName()->shouldReturn('index.html.twig');
    }

    public function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    public function its_type_is_mutable()
    {
        $this->setType('type');
        $this->getType()->shouldReturn('type');
    }

    public function it_doesnt_have_fluent_interface()
    {
        $this->setType('type')->shouldNotReturn($this);
        $this->setTemplateName('index.html.twig')->shouldNotReturn($this);
    }
}
