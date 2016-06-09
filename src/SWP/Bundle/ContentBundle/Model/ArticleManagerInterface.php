<?php

namespace SWP\Bundle\ContentBundle\Model;

interface ArticleManagerInterface
{
    public function getChildrenBy($path);

    public function createNew();

    public function updateArticle(ArticleInterface $article);

    public function getObjectClass();
}
