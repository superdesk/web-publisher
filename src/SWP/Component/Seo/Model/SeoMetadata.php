<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;

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
     * @var File|null
     */
    protected $metaImageFile;

    /**
     * @var string|null
     */
    protected $metaImageName;

    /**
     * @var File|null
     */
    protected $ogImageFile;

    /**
     * @var string|null
     */
    protected $ogImageName;

    /**
     * @var File|null
     */
    protected $twitterImageFile;

    /**
     * @var string|null
     */
    protected $twitterImageName;

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

    public function getMetaImageFile(): ?File
    {
        return $this->metaImageFile;
    }

    public function setMetaImageFile(?File $metaImageFile): void
    {
        $this->metaImageFile = $metaImageFile;
    }

    public function getMetaImageName(): ?string
    {
        return $this->metaImageName;
    }

    public function setMetaImageName(?string $metaImageName): void
    {
        $this->metaImageName = $metaImageName;
    }

    public function getOgImageFile(): ?File
    {
        return $this->ogImageFile;
    }

    public function setOgImageFile(?File $ogImageFile): void
    {
        $this->ogImageFile = $ogImageFile;
    }

    public function getOgImageName(): ?string
    {
        return $this->ogImageName;
    }

    public function setOgImageName(?string $ogImageName): void
    {
        $this->ogImageName = $ogImageName;
    }

    public function getTwitterImageFile(): ?File
    {
        return $this->twitterImageFile;
    }

    public function setTwitterImageFile(?File $twitterImageFile): void
    {
        $this->twitterImageFile = $twitterImageFile;
    }

    public function getTwitterImageName(): ?string
    {
        return $this->twitterImageName;
    }

    public function setTwitterImageName(?string $twitterImageName): void
    {
        $this->twitterImageName = $twitterImageName;
    }
}
