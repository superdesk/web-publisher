<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\TemplatesSystem\Gimme\Meta;

use PhpSpec\ObjectBehavior;

class MetaCollectionSpec extends ObjectBehavior
{
    protected $metaCollection;

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection');
    }

    public function it_should_return_total_items_count()
    {
        $this->getTotalItemsCount()->shouldReturn(0);
    }

    public function it_should_return_empty_array_when_no_items()
    {
        $this->getValues()->shouldReturn([]);
    }

    public function it_should_return_items()
    {
        $this->add('element1');
        $this->add('element2');
        $this->setTotalItemsCount(2);
        $this->getValues()->shouldReturn(['element1', 'element2']);
        $this->getTotalItemsCount()->shouldReturn(2);
    }
}
