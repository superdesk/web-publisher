<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ArticleSourceInterface extends TimestampableInterface, PersistableInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return ArticleInterface|null
     */
    public function getArticle();

    /**
     * @param ArticleInterface $article
     */
    public function setArticle(ArticleInterface $article = null);
}
