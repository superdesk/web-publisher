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
use Facebook\InstantArticles\Parser\Parser;
use Symfony\Component\Templating\EngineInterface;

class TemplateParser
{
    const FBIA_TEMPLATE_NAME = 'facebook_instant_article.html.twig';

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * TemplateParser constructor.
     *
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function parse(string $html = null): InstantArticle
    {
        $parser = new Parser();

        if (null === $html) {
            $html = $this->renderTemplate();
        }

        $logger = \Logger::getLogger('facebook-instantarticles-transformer');
        $logger->getParent()->removeAllAppenders();

        return $parser->parse($html);
    }

    public function renderTemplate(): string
    {
        return $this->getTemplating()->render('platforms/'.self::FBIA_TEMPLATE_NAME);
    }

    /**
     * @return EngineInterface
     */
    public function getTemplating()
    {
        return $this->templating;
    }
}
