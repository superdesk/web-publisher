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

use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Common\Model\TimestampableTrait;

/**
 * ArticleMedia represents media which belongs to Article.
 */
abstract class ArticleMedia implements ArticleMediaInterface
{
    use TimestampableTrait;

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
     * @param FileInterface $file
     *
     * @return ArticleMedia
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
     * @param ImageInterface $image
     *
     * @return ArticleMedia
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
     * @param ArticleInterface $article
     *
     * @return ArticleMedia
     */
    public function setArticle(ArticleInterface $article)
    {
        $this->article = $article;

        return $this;
    }

    public function getAssetId()
    {
        if ($this->getImage() instanceof Image) {
            return $this->getImage()->getAssetId();
        } elseif ($this->getFile() instanceof File) {
            return $this->getFile()->getAssetId();
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ArticleMedia
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
     * @param string $located
     *
     * @return ArticleMedia
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
     * @param string $byLine
     *
     * @return ArticleMedia
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
     * @param string $body
     *
     * @return ArticleMedia
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param string $mimetype
     *
     * @return ArticleMedia
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsageTerms()
    {
        return $this->usageTerms;
    }

    /**
     * @param mixed $usageTerms
     *
     * @return ArticleMedia
     */
    public function setUsageTerms($usageTerms)
    {
        $this->usageTerms = $usageTerms;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param ItemInterface $item
     */
    public function setFromItem(ItemInterface $item)
    {
        $this->setBody($item->getBody() ?: $item->getBodyText());
        $this->setByLine($item->getByLine());
        $this->setLocated($item->getLocated());
        $this->setDescription($item->getDescription());
        $this->setUsageTerms($item->getUsageTerms());
    }

    /**
     * {@inheritdoc}
     */
    public static function handleMediaId($mediaId)
    {
        $mediaId = preg_replace('/\\.[^.\\s]{3,4}$/', '', $mediaId);
        $mediaIdElements = explode('/', $mediaId);
        if (count($mediaIdElements) == 2) {
            return $mediaIdElements[1];
        }

        return $mediaId;
    }
}
