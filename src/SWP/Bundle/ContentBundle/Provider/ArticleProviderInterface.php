<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
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
    public function getOneById($id);

    /**
     * Gets parent article.
     *
     * @param $id
     *
     * @return ArticleInterface
     */
    public function getParent($id);
}
