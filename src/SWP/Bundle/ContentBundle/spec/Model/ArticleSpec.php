<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\MetadataAwareInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;

/**
 * @mixin Article
 */
class ArticleSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Article::class);
    }

    public function it_should_implement_article_interface()
    {
        $this->shouldImplement(ArticleInterface::class);
        $this->shouldImplement(MetadataAwareInterface::class);
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_body_by_default()
    {
        $this->getBody()->shouldReturn(null);
    }

    public function its_body_is_mutable()
    {
        $this->setBody('article body');
        $this->getBody()->shouldReturn('article body');
    }

    public function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    public function its_slug_is_mutable()
    {
        $this->setSlug('slug');
        $this->getSlug()->shouldReturn('slug');
    }

    public function it_has_no_published_at_date_by_default()
    {
        $this->getPublishedAt()->shouldReturn(null);
    }

    public function it_has_status_by_default()
    {
        $this->getStatus()->shouldReturn(ArticleInterface::STATUS_NEW);
    }

    public function its_status_is_mutable()
    {
        $this->setStatus('new');
        $this->getStatus()->shouldReturn('new');
    }

    public function it_has_no_route_by_default()
    {
        $this->getRoute()->shouldReturn(null);
    }

    public function it_should_unset_route(RouteInterface $route)
    {
        $this->setRoute($route);
        $this->setRoute();
        $this->getRoute()->shouldReturn(null);
    }

    public function its_route_is_mutable(RouteInterface $route)
    {
        $this->setRoute($route);
        $this->getRoute()->shouldReturn($route);
    }

    public function it_has_no_template_by_default()
    {
        $this->getTemplateName()->shouldReturn(null);
    }

    public function its_template_name_is_mutable()
    {
        $this->setTemplateName('index.html.twig');
        $this->getTemplateName()->shouldReturn('index.html.twig');
    }

    public function it_has_no_locale_by_default()
    {
        $this->getLocale()->shouldReturn(null);
    }

    public function its_locale_is_mutable()
    {
        $this->setLocale('en');
        $this->getLocale()->shouldReturn('en');
    }

    public function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    public function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    public function it_doesnt_have_fluent_interface(RouteInterface $route, \DateTime $date)
    {
        $this->setTitle('some title')->shouldNotReturn($this);
        $this->setSlug('some-title')->shouldNotReturn($this);
        $this->setBody('body')->shouldNotReturn($this);
        $this->setStatus(ArticleInterface::STATUS_NEW)->shouldNotReturn($this);
        $this->setRoute($route)->shouldNotReturn($this);
        $this->setTemplateName('index.html.twig')->shouldNotReturn($this);
        $this->setLocale('en')->shouldNotReturn($this);
        $this->setCreatedAt($date)->shouldNotReturn($this);
        $this->setDeletedAt($date)->shouldNotReturn($this);
        $this->setUpdatedAt($date)->shouldNotReturn($this);
        $this->setLead('lead')->shouldNotReturn($this);
    }

    public function it_should_return_true_if_article_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();
    }

    public function it_should_return_false_if_article_is_not_deleted()
    {
        $this->shouldNotBeDeleted();
    }

    public function it_has_no_deleted_at_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    public function it_has_no_metadata_by_default()
    {
        $this->getMetadata()->shouldReturn([]);
    }

    public function its_metadata_is_mutable()
    {
        $this->setMetadata(['meta1' => 'value1']);
        $this->getMetadata()->shouldReturn(['meta1' => 'value1']);
    }

    public function it_has_no_lead_by_default()
    {
        $this->getLead()->shouldReturn(null);
    }

    public function its_lead_is_mutable()
    {
        $this->setLead('lead');
        $this->getLead()->shouldReturn('lead');
    }

    public function it_has_no_keywords_by_default()
    {
        $this->getKeywords()->shouldHaveType(ArrayCollection::class);
    }

    public function its_keywords_is_mutable()
    {
        $keywordsCollection = new ArrayCollection(['keyword1', 'keyword2']);
        $this->setKeywords($keywordsCollection);
        $this->getKeywords()->shouldReturn($keywordsCollection);
    }

    public function its_code_is_required()
    {
        $this->shouldThrow('\TypeError')->during('getCode');
    }

    public function its_code_is_mutable()
    {
        $this->setCode('urn:235:sdkfsdfsdkgs');
        $this->getCode()->shouldReturn('urn:235:sdkfsdfsdkgs');
    }

    public function it_has_no_authors_by_default()
    {
        $this->getAuthors()->shouldHaveType(ArrayCollection::class);
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
