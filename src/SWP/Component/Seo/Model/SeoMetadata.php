<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableTrait;

class SeoMetadata implements SeoMetadataInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $metaTitle;

    /**
     * @var string|null
     */
    protected $metaDescription;

    /**
     * @var string|null
     */
    protected $ogTitle;

    /**
     * @var string|null
     */
    protected $ogDescription;

    /**
     * @var string|null
     */
    protected $twitterTitle;

    /**
     * @var string|null
     */
    protected $twitterDescription;

    /**
     * @var SeoImageInterface|null
     */
    protected $metaMedia;

    /**
     * @var SeoImageInterface|null
     */
    protected $ogMedia;

    /**
     * @var SeoImageInterface|null
     */
    protected $twitterMedia;

    public function getId(): string
    {
        return $this->id;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    public function setOgTitle(?string $ogTitle): void
    {
        $this->ogTitle = $ogTitle;
    }

    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    public function setOgDescription(?string $ogDescription): void
    {
        $this->ogDescription = $ogDescription;
    }

    public function getTwitterTitle(): ?string
    {
        return $this->twitterTitle;
    }

    public function setTwitterTitle(?string $twitterTitle): void
    {
        $this->twitterTitle = $twitterTitle;
    }

    public function getTwitterDescription(): ?string
    {
        return $this->twitterDescription;
    }

    public function setTwitterDescription(?string $twitterDescription): void
    {
        $this->twitterDescription = $twitterDescription;
    }

    public function getMetaMedia(): ?SeoImageInterface
    {
        return $this->metaMedia;
    }

    public function setMetaMedia(?SeoImageInterface $metaMedia): void
    {
        $this->metaMedia = $metaMedia;
    }

    public function getOgMedia(): ?SeoImageInterface
    {
        return $this->ogMedia;
    }

    public function setOgMedia(?SeoImageInterface $ogMedia): void
    {
        $this->ogMedia = $ogMedia;
    }

    public function getTwitterMedia(): ?SeoImageInterface
    {
        return $this->twitterMedia;
    }

    public function setTwitterMedia(?SeoImageInterface $twitterMedia): void
    {
        $this->twitterMedia = $twitterMedia;
    }
}
