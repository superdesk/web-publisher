<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
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

    public function it_has_no_services_by_default()
    {
        $this->getServices()->shouldReturn([]);
    }

    public function its_services_is_mutable()
    {
        $this->setServices(['sub1', 'sub2']);
        $this->getServices()->shouldReturn(['sub1', 'sub2']);
    }

    public function its_gives_services_by_name()
    {
        $this->setServices([['code' => 'sub1', 'name' => 'SUB1']]);
        $this->getServicesNames()->shouldReturn(['SUB1']);

        $this->setServices(['sub1', 'sub2']);
        $this->getServicesNames()->shouldReturn(['sub1', 'sub2']);
    }

    public function its_gives_services_by_codes()
    {
        $this->setServices([['code' => 'sub1', 'name' => 'SUB1']]);
        $this->getServicesCodes()->shouldReturn(['sub1']);

        $this->setServices(['sub1', 'sub2']);
        $this->getServicesCodes()->shouldReturn(['sub1', 'sub2']);
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

    public function it_has_urgency_by_default()
    {
        $this->getUrgency()->shouldReturn(0);
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

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable()
    {
        $this->setDescription(1);
        $this->getDescription()->shouldReturn(1);
    }

    public function it_has_no_keywords_by_default()
    {
        $this->getKeywords()->shouldReturn([]);
    }

    public function its_keywords_is_mutable()
    {
        $this->setKeywords(['keyword1', 'keyword2']);
        $this->getKeywords()->shouldReturn(['keyword1', 'keyword2']);
    }

    public function its_gets_all_metadata()
    {
        $metadata = [
            'subject' => $this->getSubjects(),
            'urgency' => $this->getUrgency(),
            'priority' => $this->getPriority(),
            'located' => $this->getLocated(),
            'place' => $this->getPlaces(),
            'service' => $this->getServices(),
            'type' => $this->getType(),
            'byline' => $this->getByline(),
            'guid' => $this->getGuid(),
            'edNote' => $this->getEdNote(),
            'genre' => $this->getGenre(),
            'language' => $this->getLanguage(),
            'profile' => $this->getProfile(),
        ];

        $this->getMetadata()->shouldReturn($metadata);
    }

    public function it_has_status_by_default()
    {
        $this->getPubStatus()->shouldReturn('usable');
    }

    public function its_status_is_mutable()
    {
        $this->setPubStatus('withheld');
        $this->getPubStatus()->shouldReturn('withheld');
    }

    public function it_has_no_extra_by_default()
    {
        $this->getExtra()->shouldReturn([]);
    }

    public function its_extra_is_mutable()
    {
        $this->setExtra(['f1' => '1', 'f2' => 'test']);
        $this->getExtra()->shouldReturn(['f1' => '1', 'f2' => 'test']);
    }
}
