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

trait ArticlesCountTrait
{
    /**
     * @var int
     */
    protected $articlesCount = 0;

    /**
     * {@inheritdoc}
     */
    public function getArticlesCount()
    {
        return $this->articlesCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticlesCount($articlesCount)
    {
        $this->articlesCount = $articlesCount;
    }
}
