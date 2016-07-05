<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Resolver;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface TemplateNameResolverInterface
{
    /**
     * @return string
     */
    public function resolveFromArticle(ArticleInterface $article);
}
