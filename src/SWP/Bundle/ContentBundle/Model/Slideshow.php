<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\TimestampableTrait;

class Slideshow implements SlideshowInterface
{
    use TimestampableTrait;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var ArticleMediaInterface[]|Collection
     */
    protected $items;

    /**
     * @var ArticleInterface|null
     */
    protected $article;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ArticleMediaInterface $articleMedia): void
    {
        if (!$this->hasItem($articleMedia)) {
            $this->items->add($articleMedia);
        }
    }

    public function removeItem(ArticleMediaInterface $articleMedia): void
    {
        if ($this->hasItem($articleMedia)) {
            $this->items->removeElement($articleMedia);
        }
    }

    public function hasItem(ArticleMediaInterface $articleMedia): bool
    {
        return $this->items->contains($articleMedia);
    }

    public function getArticle(): ?ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(?ArticleInterface $article): void
    {
        $this->article = $article;
    }
}
