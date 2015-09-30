<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Document;

class Article extends BaseDocument implements
    TranslatableDocumentInterface,
    VersionableDocumentInterface
{
    use TranslatableDocumentTrait, VersionableDocumentTrait;

    /**
     * Article title
     *
     * @var string
     */
    protected $title;

    /**
     * Article content
     *
     * @var string
     */
    protected $content;

    /**
     * Status of the article
     *
     * @var ['y','n','s']
     */
    protected $status;

    /**
     * Publication date of article
     *
     * @var \DateTime|null Returns null when not published
     */
    protected $published;

    /**
     * Article metadata
     *
     * @var ArticleMetadata[]
     */
    protected $metadata;

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param mixed $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param mixed $content the content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return ['y','n','s']
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param ['y','n','s'] $status Value to set
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets the value of published.
     *
     * @return \DateTime|null
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Sets the value of published.
     *
     * @param \DateTime|null $published Value to set
     *
     * @return self
     */
    public function setPublished(\DateTime $published = null)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Gets the value of metadata.
     *
     * @return ArticleMetadata[]
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Sets the value for metadata.
     *
     * @param ArticleMetadata[] $metadata Value to set
     *
     * @return self
     */
    public function setMetadata(array $metadata = null)
    {
        $this->metadata = $metadata;

        return $this;
    }
}
