<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Parser;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Transformer;
use Symfony\Component\Templating\EngineInterface;

interface TemplateParserInterface
{
    /**
     * @param string|null $html
     *
     * @return InstantArticle
     */
    public function parse(string $html = null): InstantArticle;

    /**
     * @return string
     */
    public function renderTemplate(): string;

    /**
     * @return Transformer
     */
    public function getTransformer(): Transformer;

    /**
     * @return EngineInterface
     */
    public function getTemplating();
}
