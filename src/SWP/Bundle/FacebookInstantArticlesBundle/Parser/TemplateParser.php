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
use Symfony\Component\Templating\EngineInterface;
use Facebook\InstantArticles\Transformer\Transformer;
use Twig\Environment;

final class TemplateParser implements TemplateParserInterface
{
    const FBIA_TEMPLATE_NAME = 'facebook_instant_article.html.twig';

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var Transformer
     */
    protected $transformer;

    /**
     * TemplateParser constructor.
     *
     * @param Environment $templating
     */
    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $html = null): InstantArticle
    {
        if (null === $html) {
            $html = $this->renderTemplate();
        }

        $logger = \Logger::getLogger('facebook-instantarticles-transformer');
        $logger->getParent()->removeAllAppenders();

        libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $document->loadHTML($html);
        libxml_use_internal_errors(false);

        $instantArticle = InstantArticle::create();
        $transformer = $this->getTransformer();
        $transformer->transform($instantArticle, $document);

        return $instantArticle;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(): string
    {
        return $this->getTemplating()->render('platforms/'.self::FBIA_TEMPLATE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformer(): Transformer
    {
        if (null !== $this->transformer) {
            return $this->transformer;
        }

        $json_file = file_get_contents(__DIR__.'/../Resources/instant-articles-rules.json');
        $this->transformer = new Transformer();
        $this->transformer->loadRules($json_file);

        return $this->transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplating()
    {
        return $this->templating;
    }
}
