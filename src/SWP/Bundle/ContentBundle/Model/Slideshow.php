<?php

declare(strict_types=1);

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
