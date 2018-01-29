<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Bridge\Model;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Model\Author;
use SWP\Component\Bridge\Model\AuthorInterface;

final class AuthorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Author::class);
    }

    function it_should_implement_an_interface()
    {
        $this->shouldImplement(AuthorInterface::class);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Andrew Powers');
        $this->getName()->shouldReturn('Andrew Powers');
    }

    function it_has_no_role_by_default()
    {
        $this->getRole()->shouldReturn(null);
    }

    function its_role_is_mutable()
    {
        $this->setRole('Writer');
        $this->getRole()->shouldReturn('Writer');
    }

    function it_has_no_jobtitle_by_default()
    {
        $this->getJobTitle()->shouldReturn([]);
    }

    function its_jobtitle_is_mutable()
    {
        $this->setJobTitle(['name' => 'Writer', 'qcode' => '1']);
        $this->getJobTitle()->shouldReturn(['name' => 'Writer', 'qcode' => '1']);
    }

    function it_has_no_biography_by_default()
    {
        $this->getRole()->shouldReturn(null);
    }

    function its_biography_is_mutable()
    {
        $this->setBiography('bio');
        $this->getBiography()->shouldReturn('bio');
    }

    function it_has_no_avatar_url_by_default()
    {
        $this->getAvatarUrl()->shouldReturn(null);
    }

    function its_avatar_url_is_mutable()
    {
        $this->setAvatarUrl('http://example.com/avatar.png');
        $this->getAvatarUrl()->shouldReturn('http://example.com/avatar.png');
    }
}
