<?php

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

namespace SWP\Bundle\CoreBundle\Processor;

use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessorInterface as BaseProcessorInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;

interface ArticleBodyProcessorInterface extends BaseProcessorInterface
{
    /**
     * @param MediaFactoryInterface $mediaFactory
     * @param PackageInterface      $package
     * @param ArticleInterface      $article
     */
    public function fillArticleMedia(MediaFactoryInterface $mediaFactory, PackageInterface $package, ArticleInterface $article);
}
