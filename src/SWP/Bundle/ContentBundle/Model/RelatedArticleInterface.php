<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface RelatedArticleInterface extends TimestampableInterface, PersistableInterface
{
    public function getId(): string;

    public function getRelatesTo(): ArticleInterface;

    public function setRelatesTo(ArticleInterface $relatesTo): void;

    public function getArticle(): ArticleInterface;

    public function setArticle(ArticleInterface $article): void;
}
