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

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Interface ArticleMediaInterface.
 */
interface ArticleMediaInterface extends PersistableInterface, SoftDeletableInterface, TimestampableInterface
{
    public const TYPE_SLIDE_SHOW = 'slide_show';

    public const TYPE_FEATURE_MEDIA = 'feature_media';

    public const TYPE_EMBEDDED_IMAGE = 'embedded_image';

    /**
     * @return ArrayCollection
     */
    public function getRenditions();

    public function addRendition(ImageRenditionInterface $rendition);

    /**
     * @param ArrayCollection $renditions
     */
    public function setRenditions($renditions);

    /**
     * @return mixed
     */
    public function getFile();

    /**
     * @param FileInterface $file
     *
     * @return ArticleMedia
     */
    public function setFile($file);

    /**
     * @return mixed
     */
    public function getImage();

    /**
     * @param ImageInterface $image
     *
     * @return ArticleMedia
     */
    public function setImage($image);

    /**
     * @return mixed
     */
    public function getArticle();

    /**
     * @return ArticleMedia
     */
    public function setArticle(ArticleInterface $article);

    /**
     * @return mixed
     */
    public function getAssetId(): ?string;

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return ArticleMedia
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getLocated();

    /**
     * @param string $located
     *
     * @return ArticleMedia
     */
    public function setLocated($located);

    /**
     * @return mixed
     */
    public function getByLine();

    /**
     * @param string $byLine
     *
     * @return ArticleMedia
     */
    public function setByLine($byLine);

    /**
     * @return mixed
     */
    public function getBody();

    /**
     * @param string $body
     *
     * @return ArticleMedia
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getMimetype();

    /**
     * @param string $mimetype
     *
     * @return ArticleMedia
     */
    public function setMimetype($mimetype);

    /**
     * @return mixed
     */
    public function getUsageTerms();

    /**
     * @param mixed $usageTerms
     *
     * @return ArticleMedia
     */
    public function setUsageTerms($usageTerms);

    public function getKey(): string;

    public function setKey(string $key);

    public function setFromItem(ItemInterface $item);

    /**
     * @param string $mediaId
     *
     * @return mixed
     */
    public static function handleMediaId($mediaId);

    /**
     * @return mixed
     */
    public static function getOriginalMediaId(string $mediaId);

    public function getHeadline(): ?string;

    public function setHeadline(?string $headline): void;

    public function getCopyrightNotice(): ?string;

    public function setCopyrightNotice(?string $copyrightNotice): void;

    public function getCopyrightHolder(): ?string;

    public function setCopyrightHolder(?string $copyrightHolder): void;

    public function getLicense(): ?License;

    public function setLicense(License $license): void;
}
