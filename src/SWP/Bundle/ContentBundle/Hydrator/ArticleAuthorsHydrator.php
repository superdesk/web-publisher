<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Hydrator;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;

final class ArticleAuthorsHydrator implements ArticleHydratorInterface
{
    public function hydrate(ArticleInterface $article, PackageInterface $package): ArticleInterface
    {
//        $article->setAuthors([]);
        foreach ($package->getAuthors()->toArray() as $author) {
            // remove all authors and add again?
            $article->addAuthor($author);
        }

        return $article;
    }
}
