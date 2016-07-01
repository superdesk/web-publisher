<?php

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface ArticleProviderInterface
{
    /**
     * Gets the article by id.
     *
     * @param $id
     *
     * @return ArticleInterface
     */
    public function findOneById($id);
}
