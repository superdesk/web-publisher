<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

use DateTimeInterface;

/**
 * See https://developer.apple.com/documentation/apple_news/metadata.
 */
class Metadata
{
    /** @var string[] */
    private $authors = [];

    /** @var string */
    private $canonicalUrl;

    /** @var DateTimeInterface */
    private $dateCreated;

    /** @var DateTimeInterface|null */
    private $dateModified;

    /** @var DateTimeInterface */
    private $datePublished;

    /** @var string */
    private $excerpt;

    /** @var string */
    private $generatorIdentifier;

    /** @var string */
    private $generatorName;

    /** @var string */
    private $generatorVersion;

    /** @var string[] */
    private $keywords = [];

    /** @var LinkedArticle[] */
    private $links = [];

    /** @var string */
    private $thumbnailURL;

    /** @var bool */
    private $transparentToolbar = false;

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function setAuthors(array $authors): void
    {
        $this->authors = $authors;
    }

    public function getCanonicalUrl(): string
    {
        return $this->canonicalUrl;
    }

    public function setCanonicalUrl(string $canonicalUrl): void
    {
        $this->canonicalUrl = $canonicalUrl;
    }

    public function getDateCreated(): DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTimeInterface $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    public function getDateModified(): ?DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(?DateTimeInterface $dateModified): void
    {
        $this->dateModified = $dateModified;
    }

    public function getDatePublished(): DateTimeInterface
    {
        return $this->datePublished;
    }

    public function setDatePublished(DateTimeInterface $datePublished): void
    {
        $this->datePublished = $datePublished;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function getGeneratorIdentifier(): string
    {
        return $this->generatorIdentifier;
    }

    public function setGeneratorIdentifier(string $generatorIdentifier): void
    {
        $this->generatorIdentifier = $generatorIdentifier;
    }

    public function getGeneratorName(): string
    {
        return $this->generatorName;
    }

    public function setGeneratorName(string $generatorName): void
    {
        $this->generatorName = $generatorName;
    }

    public function getGeneratorVersion(): string
    {
        return $this->generatorVersion;
    }

    public function setGeneratorVersion(string $generatorVersion): void
    {
        $this->generatorVersion = $generatorVersion;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getThumbnailURL(): string
    {
        return $this->thumbnailURL;
    }

    public function setThumbnailURL(string $thumbnailURL): void
    {
        $this->thumbnailURL = $thumbnailURL;
    }

    public function isTransparentToolbar(): bool
    {
        return $this->transparentToolbar;
    }

    public function setTransparentToolbar(bool $transparentToolbar): void
    {
        $this->transparentToolbar = $transparentToolbar;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }
}
