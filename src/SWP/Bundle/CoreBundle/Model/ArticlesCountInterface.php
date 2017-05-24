<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

interface ArticlesCountInterface
{
    /**
     * @return mixed
     */
    public function getArticlesCount();

    /**
     * @param $articlesCount
     *
     * @return mixed
     */
    public function setArticlesCount($articlesCount);
}
