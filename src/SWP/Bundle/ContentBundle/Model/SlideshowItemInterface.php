<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SlideshowItemInterface extends PersistableInterface, TimestampableInterface, SoftDeletableInterface
{
    public function getArticleMedia(): ArticleMediaInterface;

    public function setArticleMedia(ArticleMediaInterface $articleMedia): void;

    public function getSlideshow(): SlideshowInterface;

    public function setSlideshow(SlideshowInterface $slideshow): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;
}
