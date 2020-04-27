<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

use SWP\Bundle\CoreBundle\AppleNews\Component\ComponentInterface;

/**
 * The root object of an Apple News article, containing required properties,
 * metadata, content, layout, and styles.
 */
class ArticleDocument
{
    public const APPLE_NEWS_FORMAT_VERSION = '1.7';

    private $title;

    private $subtitle = '';

    private $components = [];

    /** @var Metadata */
    private $metadata;

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * @return ComponentTextStyles
     */
    public function getComponentTextStyles(): ComponentTextStyles
    {
        return $this->componentTextStyles;
    }

    private $identifier;

    private $language;

    private $version = self::APPLE_NEWS_FORMAT_VERSION;

    /** @var Layout */
    private $layout;

    /** @var ComponentTextStyles */
    private $componentTextStyles;

    /**
     * @param ComponentTextStyles $componentTextStyles
     */
    public function setComponentTextStyles(ComponentTextStyles $componentTextStyles): void
    {
        $this->componentTextStyles = $componentTextStyles;
    }

    public function addComponent(ComponentInterface $component): void
    {
        $this->components[] = $component;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function setLayout(Layout $layout): void
    {
        $this->layout = $layout;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function setMetadata(Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }
}
