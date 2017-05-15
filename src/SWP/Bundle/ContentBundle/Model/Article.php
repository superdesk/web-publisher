<?php

declare(strict_types=1);

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

namespace SWP\Bundle\ContentBundle\Model;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\Common\Model\TranslatableTrait;

/**
 * Class Article.
 */
class Article implements ArticleInterface, MediaAwareArticleInterface
{
    use TranslatableTrait, SoftDeletableTrait, TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var \DateTime
     */
    protected $publishedAt;

    /**
     * @var string
     */
    protected $status = ArticleInterface::STATUS_NEW;

    /**
     * @var RouteInterface
     */
    protected $route;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var \DateTime
     */
    protected $publishStartDate;

    /**
     * @var \DateTime
     */
    protected $publishEndDate;

    /**
     * @var bool
     */
    protected $isPublishable;

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var Collection
     */
    protected $media;

    /**
     * @var ArticleMediaInterface
     */
    protected $featureMedia;

    /**
     * @var string
     */
    protected $lead;

    /**
     * @var array
     */
    protected $keywords = [];

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $source;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setPublishable(false);
        $this->setMedia(new ArrayCollection());
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishStartDate(\DateTime $startDate = null)
    {
        $this->publishStartDate = $startDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishEndDate(\DateTime $endDate = null)
    {
        $this->publishEndDate = $endDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishable()
    {
        return $this->isPublishable;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishable($boolean)
    {
        $this->isPublishable = $boolean;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return $this->getStatus() === ArticleInterface::STATUS_PUBLISHED;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoute(RouteInterface $route = null)
    {
        $this->route = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * {@inheritdoc}
     */
    public function setMedia(Collection $media)
    {
        $this->media = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->setSlug(Transliterator::urlize($this->title));
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = Transliterator::urlize($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataByKey(string $key)
    {
        $metadata = $this->getMetadata();

        if (isset($metadata[$key])) {
            return $metadata[$key];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectType()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * {@inheritdoc}
     */
    public function setLead($lead)
    {
        $this->lead = $lead;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatureMedia()
    {
        return $this->featureMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeatureMedia(ArticleMediaInterface $featureMedia = null)
    {
        $this->featureMedia = $featureMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function setSource($source)
    {
        $this->source = $source;
    }
}
