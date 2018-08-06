<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SlideshowInterface extends PersistableInterface, TimestampableInterface
{
    public function getCode(): string;

    public function setCode(string $code): void;

    public function getArticle(): ?ArticleInterface;

    public function setArticle(?ArticleInterface $article): void;

    public function getItems(): Collection;

    public function addItem(ArticleMediaInterface $articleMedia): void;

    public function removeItem(ArticleMediaInterface $articleMedia): void;

    public function hasItem(ArticleMediaInterface $articleMedia): bool;
}
