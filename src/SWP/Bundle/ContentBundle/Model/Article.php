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
use SWP\Bundle\ContentBundle\Doctrine\ORM\TimestampableCancelTrait;
use SWP\Component\Bridge\Model\AuthorsAwareTrait;
use SWP\Component\Common\Model\DateTime;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\Common\Model\TranslatableTrait;
use SWP\Component\Seo\Model\SeoMetadataAwareTrait;

class Article implements ArticleInterface
{
    use TranslatableTrait;
    use SoftDeletableTrait;
    use TimestampableTrait;
    use AuthorsAwareTrait;
    use KeywordsAwareTrait;
    use RelatedArticlesAwareTrait;
    use TimestampableCancelTrait;
    use SeoMetadataAwareTrait;
    use MediaAwareTrait;

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
     * @var MetadataInterface|null
     */
    protected $data;

    /**
     * @var string
     */
    protected $lead;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var Collection|ArticleSourceInterface[]
     */
    protected $sources;

    /**
     * @var array|null
     */
    protected $extra;

    /**
     * @var Collection|SlideshowInterface[]
     */
    protected $slideshows;

    /** @var Collection|ArticlePreviousRelativeUrlInterface[] * */
    protected $previousRelativeUrls;

    /** @var array */
    protected $metadata = [];

    public function __construct()
    {
        $this->createdAt = DateTime::getCurrentDateTime();
        $this->setPublishable(false);
        $this->setMedia(new ArrayCollection());
        $this->sources = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->keywords = new ArrayCollection();
        $this->slideshows = new ArrayCollection();
        $this->relatedArticles = new ArrayCollection();
        $this->previousRelativeUrls = new ArrayCollection();
    }

    public function setPublishStartDate(\DateTime $startDate = null)
    {
        $this->publishStartDate = $startDate;
    }

    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    public function setPublishEndDate(\DateTime $endDate = null)
    {
        $this->publishEndDate = $endDate;
    }

    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    public function isPublishable()
    {
        return $this->isPublishable;
    }

    public function setPublishable($boolean)
    {
        $this->isPublishable = $boolean;
    }

    public function setIsPublishable(bool $boolean): void
    {
        $this->setPublishable($boolean);
    }

    public function isPublished()
    {
        return ArticleInterface::STATUS_PUBLISHED === $this->getStatus();
    }

    public function setRoute(RouteInterface $route = null)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = \trim($body);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getPlace(): ?array
    {
        $metadata = $this->getMetadata();

        if (isset($metadata['place']) && is_array($metadata['place']) && count($metadata['place']) > 0) {
            return $metadata['place'][array_key_first($metadata['place'])];
        }

        return null;
    }

    public function getPlaces(): array
    {
        $metadata = $this->getMetadata();

        if (isset($metadata['place']) && is_array($metadata['place']) && count($metadata['place']) > 0) {
            return $metadata['place'];
        }

        return [];
    }

    public function setTitle($title)
    {
        $this->title = $title;

        if (null !== $this->slug && '' !== $this->slug) {
            $this->setSlug($this->slug);

            return;
        }

        $this->setSlug($this->title);
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $urlizedSlug = Transliterator::urlize($slug);

        if ('' === $urlizedSlug) {
            $slug = str_replace('\'', '-', $slug);
            $this->slug = Transliterator::transliterate($slug);

            return;
        }

        $this->slug = $urlizedSlug;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    public function getMetadataByKey(string $key)
    {
        $metadata = $this->getMetadata();

        if (isset($metadata[$key])) {
            return $metadata[$key];
        }
    }

    public function setData(?MetadataInterface $metadata): void
    {
        if (null !== $metadata) {
            $metadata->setArticle($this);
        }

        $this->data = $metadata;
    }

    public function getData(): ?MetadataInterface
    {
        return $this->data;
    }

    public function getSubjectType()
    {
        return 'article';
    }

    public function getLead()
    {
        return $this->lead;
    }

    public function setLead($lead)
    {
        $this->lead = $lead;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }

    public function addSourceReference(ArticleSourceReferenceInterface $source)
    {
        if (!$this->hasSourceReference($source)) {
            $this->sources->add($source);
        }
    }

    public function removeSourceReference(ArticleSourceReferenceInterface $source)
    {
        $this->sources->removeElement($source);
    }

    public function hasSourceReference(ArticleSourceReferenceInterface $source): bool
    {
        return $this->sources->contains($source);
    }

    public function getSources(): Collection
    {
        if (0 < $this->sources->count()) {
            $sources = new ArrayCollection();
            /** @var ArticleSourceReferenceInterface $source */
            foreach ($this->sources as $source) {
                $sources->add($source->getArticleSource());
            }

            return $sources;
        }

        return $this->sources;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function setExtra(?array $extra): void
    {
        $this->extra = $extra;
    }

    public function getSlideshows(): Collection
    {
        return $this->slideshows;
    }

    public function hasSlideshow(SlideshowInterface $slideshow): bool
    {
        return $this->slideshows->contains($slideshow);
    }

    public function addSlideshow(SlideshowInterface $slideshow): void
    {
        if (!$this->hasSlideshow($slideshow)) {
            $slideshow->setArticle($this);
            $this->slideshows->add($slideshow);
        }
    }

    public function removeSlideshow(SlideshowInterface $slideshow): void
    {
        if ($this->hasSlideshow($slideshow)) {
            $slideshow->setArticle(null);
            $this->slideshows->removeElement($slideshow);
        }
    }

    public function getPreviousRelativeUrl(): Collection
    {
        return $this->previousRelativeUrls;
    }

    public function hasPreviousRelativeUrl(ArticlePreviousRelativeUrlInterface $previousRelativeUrl): bool
    {
        return $this->previousRelativeUrls->contains($previousRelativeUrl);
    }

    public function addPreviousRelativeUrl(ArticlePreviousRelativeUrlInterface $previousRelativeUrl): void
    {
        if (!$this->hasPreviousRelativeUrl($previousRelativeUrl)) {
            $previousRelativeUrl->setArticle($this);
            $this->previousRelativeUrls->add($previousRelativeUrl);
        }
    }

    public function removePreviousRelativeUrl(ArticlePreviousRelativeUrlInterface $previousRelativeUrl): void
    {
        if ($this->hasPreviousRelativeUrl($previousRelativeUrl)) {
            $previousRelativeUrl->setArticle(null);
            $this->previousRelativeUrls->removeElement($previousRelativeUrl);
        }
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
