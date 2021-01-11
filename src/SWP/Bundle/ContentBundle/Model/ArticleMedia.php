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

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class ArticleMedia implements ArticleMediaInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @var ImageInterface
     */
    protected $image;

    /**
     * @var ArticleInterface
     */
    protected $article;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $located;

    /**
     * @var string
     */
    protected $byLine;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $mimetype;

    /**
     * @var string
     */
    protected $usageTerms;

    /**
     * @var ArrayCollection
     */
    protected $renditions;

    /**
     * @var string|null
     */
    protected $headline;

    /**
     * @var string|null
     */
    protected $copyrightHolder;

    /**
     * @var string/null
     */
    protected $copyrightNotice;

    /** @var string|null */
    protected $mediaType = ArticleMediaInterface::TYPE_EMBEDDED_IMAGE;

    /** @var License|null */
    protected $license;

    /**
     * ArticleMedia constructor.
     */
    public function __construct()
    {
        $this->renditions = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * {@inheritdoc}
     */
    public function addRendition(ImageRenditionInterface $rendition)
    {
        $this->renditions->add($rendition);
    }

    /**
     * {@inheritdoc}
     */
    public function setRenditions($renditions)
    {
        $this->renditions = $renditions;
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticle(ArticleInterface $article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetId(): ?string
    {
        if ($this->getImage() instanceof Image) {
            return $this->getImage()->getAssetId();
        }

        if ($this->getFile() instanceof File) {
            return $this->getFile()->getAssetId();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocated()
    {
        return $this->located;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocated($located)
    {
        $this->located = $located;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getByLine()
    {
        return $this->byLine;
    }

    /**
     * {@inheritdoc}
     */
    public function setByLine($byLine)
    {
        $this->byLine = $byLine;

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageTerms()
    {
        return $this->usageTerms;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageTerms($usageTerms)
    {
        $this->usageTerms = $usageTerms;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    public function setHeadline(?string $headline): void
    {
        $this->headline = $headline;
    }

    public function getCopyrightNotice(): ?string
    {
        return $this->copyrightNotice;
    }

    public function setCopyrightNotice(?string $copyrightNotice): void
    {
        $this->copyrightNotice = $copyrightNotice;
    }

    public function getCopyrightHolder(): ?string
    {
        return $this->copyrightHolder;
    }

    public function setCopyrightHolder(?string $copyrightHolder): void
    {
        $this->copyrightHolder = $copyrightHolder;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(?string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    /**
     * {@inheritdoc}
     */
    public function setFromItem(ItemInterface $item)
    {
        $this->setBody($item->getBody() ?: $item->getBodyText());
        $this->setByLine($item->getByLine());
        $this->setLocated($item->getLocated());
        $this->setDescription($item->getDescription());
        $this->setUsageTerms($item->getUsageTerms());
        $this->setHeadline($item->getHeadline());
        $this->setCopyrightHolder($item->getCopyrightHolder());
        $this->setCopyrightNotice($item->getCopyrightNotice());
    }

    /**
     * {@inheritdoc}
     */
    public static function handleMediaId($mediaId)
    {
        $mediaId = preg_replace('/\\.[^.\\s]{3,4}$/', '', $mediaId);
        $mediaIdElements = explode('/', $mediaId);
        if (count($mediaIdElements) > 1) {
            return implode('_', $mediaIdElements);
        }

        return $mediaId;
    }

    /**
     * {@inheritdoc}
     */
    public static function getOriginalMediaId(string $mediaId)
    {
        return str_replace('_', '/', $mediaId);
    }

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(License $license): void
    {
        $this->license = $license;
    }
}
