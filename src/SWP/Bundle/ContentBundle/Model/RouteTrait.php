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

trait RouteTrait
{
    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $articlesTemplateName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $cacheTimeInSeconds = 0;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected string $variablePattern = '';

    /**
     * @var string
     */
    protected string $staticPrefix = '';

    /**
     * @var int
     */
    protected $position;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Rename a route by setting its new name.
     *
     * @param string $name the new name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Slug is used for static prefix generation.
     *
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getVariablePattern(): string
    {
        return $this->variablePattern;
    }

    /**
     * @param string $variablePattern
     */
    public function setVariablePattern(?string $variablePattern): static
    {
        $this->variablePattern = (string) $variablePattern;

        return $this;
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
    public function getArticlesTemplateName()
    {
        return $this->articlesTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticlesTemplateName($articlesTemplateName)
    {
        $this->articlesTemplateName = $articlesTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getCacheTimeInSeconds()
    {
        return $this->cacheTimeInSeconds;
    }

    /**
     * @param int $cacheTimeInSeconds
     */
    public function setCacheTimeInSeconds($cacheTimeInSeconds)
    {
        $this->cacheTimeInSeconds = $cacheTimeInSeconds;
    }

    /**
     * @return string
     */
    public function getStaticPrefix(): string
    {
        return $this->staticPrefix;
    }

    public function clearContent(): static
    {
        $this->content = null;

        return $this;
    }

    public function getStaticPrefixForSerialization(): ?string
    {
        return '' === $this->staticPrefix ? null : $this->staticPrefix;
    }

    public function getVariablePatternForSerialization(): ?string
    {
        return '' === $this->variablePattern ? null : $this->variablePattern;
    }

    /**
     * @param string $staticPrefix
     */
    public function setStaticPrefix(string $staticPrefix): static
    {
        $this->staticPrefix = $staticPrefix;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position)
    {
        $this->position = $position;
    }
}
