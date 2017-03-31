<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Cloner;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;

interface ArticleClonerInterface
{
    /**
     * @param ArticleInterface $article
     * @param array            $config
     *
     * @return ArticleInterface
     */
    public function clone(ArticleInterface $article, array $config = []): ArticleInterface;

    /**
     * @param array $config
     */
    public function validateConfig(array $config = []);
}
