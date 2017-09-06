<?php

declare(strict_types=1);

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

class ArticleSourceReference implements ArticleSourceReferenceInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var ArticleInterface
     */
    protected $article;

    /**
     * @var ArticleSource
     */
    protected $articleSource;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticle(ArticleInterface $article)
    {
        $this->article = $article;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticleSource(): ArticleSourceInterface
    {
        return $this->articleSource;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticleSource(ArticleSourceInterface $articleSource)
    {
        $this->articleSource = $articleSource;
    }
}
