<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Enhancer;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

/**
 * Interface EnhancerInterface.
 */
interface EnhancerInterface
{
    /**
     * @param ArticleInterface $article
     *
     * @return ArticleInterface
     */
    public function enhance(ArticleInterface $article);
}
