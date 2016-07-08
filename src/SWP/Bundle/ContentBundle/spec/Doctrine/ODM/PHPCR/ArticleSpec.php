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
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

/**
 * @mixin Article
 */
class ArticleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Article::class);
        $this->shouldHaveType(\SWP\Bundle\ContentBundle\Model\Article::class);
    }

    function it_should_implement_interfaces()
    {
        $this->shouldImplement(ArticleInterface::class);
        $this->shouldImplement(HierarchyInterface::class);
    }

    function it_has_no_parent_by_default()
    {
        $this->getParent()->shouldReturn(null);
    }

    function its_parent_is_mutable()
    {
        $object = new \stdClass();
        $this->setParentDocument($object);
        $this->getParentDocument()->shouldReturn($object);
    }

    function it_has_no_children_by_default()
    {
        $this->getChildren()->shouldReturn(null);
    }

    public function it_doesnt_have_fluent_interface()
    {
        $object = new \stdClass();
        $this->setParentDocument($object)->shouldNotReturn($this);
    }
}
