<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\Bridge\Model;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Model\BaseContent;
use SWP\Component\Bridge\Model\ContentInterface;

/**
 * @mixin BaseContent
 */
class BaseContentSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(BaseContent::class);
    }

    public function it_should_implement_interfaces()
    {
        $this->shouldImplement(ContentInterface::class);
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_guid_by_default()
    {
        $this->getGuid()->shouldReturn(null);
    }

    public function its_guid__is_mutable()
    {
        $this->setGuid('21EC2020-3AEA-4069-A2DD-08002B30309D');
        $this->getGuid()->shouldReturn('21EC2020-3AEA-4069-A2DD-08002B30309D');
    }

    public function it_has_no_headline_by_default()
    {
        $this->getHeadline()->shouldReturn(null);
    }

    public function its_headline_is_mutable()
    {
        $this->setHeadline('headline');
        $this->getHeadline()->shouldReturn('headline');
    }

    public function it_has_no_byline_by_default()
    {
        $this->getByline()->shouldReturn(null);
    }

    public function its_byline_is_mutable()
    {
        $this->setByline('byline');
        $this->getByline()->shouldReturn('byline');
    }

    public function it_has_no_slugline_by_default()
    {
        $this->getSlugline()->shouldReturn(null);
    }

    public function its_slugline_is_mutable()
    {
        $this->setSlugline('slugline');
        $this->getSlugline()->shouldReturn('slugline');
    }

    public function it_has_no_language_by_default()
    {
        $this->getLanguage()->shouldReturn(null);
    }

    public function its_language_is_mutable()
    {
        $this->setLanguage('en');
        $this->getLanguage()->shouldReturn('en');
    }

    public function it_has_no_subjects_by_default()
    {
        $this->getSubjects()->shouldReturn([]);
    }

    public function its_subjects_is_mutable()
    {
        $this->setSubjects(['sub1', 'sub2']);
        $this->getSubjects()->shouldReturn(['sub1', 'sub2']);
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

    public function it_has_no_places_by_default()
    {
        $this->getPlaces()->shouldReturn([]);
    }

    public function its_places_is_mutable()
    {
        $this->setPlaces(['Italy', 'Poland']);
        $this->getPlaces()->shouldReturn(['Italy', 'Poland']);
    }

    public function it_has_no_located_by_default()
    {
        $this->getLocated()->shouldReturn(null);
    }

    public function its_located_is_mutable()
    {
        $this->setLocated('Paris');
        $this->getLocated()->shouldReturn('Paris');
    }

    public function it_has_no_urgency_by_default()
    {
        $this->getUrgency()->shouldReturn(null);
    }

    public function its_urgency_is_mutable()
    {
        $this->setUrgency(1);
        $this->getUrgency()->shouldReturn(1);
    }

    public function it_has_no_priority_by_default()
    {
        $this->getPriority()->shouldReturn(null);
    }

    public function its_priority_is_mutable()
    {
        $this->setPriority(1);
        $this->getPriority()->shouldReturn(1);
    }

    public function it_has_no_version_by_default()
    {
        $this->getVersion()->shouldReturn(null);
    }

    public function its_version_is_mutable()
    {
        $this->setVersion(1);
        $this->getVersion()->shouldReturn(1);
    }
}
