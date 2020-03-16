<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class SlideshowItem implements SlideshowItemInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var ArticleMediaInterface
     */
    protected $articleMedia;

    /**
     * @var SlideshowInterface
     */
    protected $slideshow;

    /** @var int|null */
    protected $position = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getArticleMedia(): ArticleMediaInterface
    {
        return $this->articleMedia;
    }

    public function setArticleMedia(ArticleMediaInterface $articleMedia): void
    {
        $this->articleMedia = $articleMedia;
    }

    public function getSlideshow(): SlideshowInterface
    {
        return $this->slideshow;
    }

    public function setSlideshow(SlideshowInterface $slideshow): void
    {
        $this->slideshow = $slideshow;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
