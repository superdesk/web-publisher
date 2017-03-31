<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;

interface ArticlePublisherInterface
{
    /**
     * @param ArticleInterface $article
     * @param array            $tenants
     */
    public function publish(ArticleInterface $article, array $tenants);
}
