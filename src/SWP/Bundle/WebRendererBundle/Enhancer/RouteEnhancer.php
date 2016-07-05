<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Enhancer;

use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;
use SWP\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Bundle\WebRendererBundle\Resolver\TemplateNameResolverInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\WebRendererBundle\Controller\ContentController;

class RouteEnhancer implements RouteEnhancerInterface
{
    protected $templateNameResolver;
    protected $metaLoader;

    public function __construct(TemplateNameResolverInterface $templateNameResolver, LoaderInterface $metaLoader)
    {
        $this->templateNameResolver = $templateNameResolver;
        $this->metaLoader = $metaLoader;
    }

    public function enhance(array $defaults, Request $request)
    {
        $content = $defaults[RouteObjectInterface::CONTENT_OBJECT];
        $defaults['_controller'] = ContentController::class . '::renderPageAction';
        $defaults = $this->setArticleMeta($content, $request, $defaults);
        $defaults = $this->setTemplateName($content, $defaults);

        return $defaults;
    }

    protected function setArticleMeta($content, $request, $defaults)
    {
        $articleMeta = null;
        if (isset($defaults['slug'])) {
            $articleMeta = $this->metaLoader->load('article', ['slug' => $defaults['slug']]);
            $defaults['type'] = RouteInterface::TYPE_COLLECTION;
            if (!$articleMeta) {
                $defaults[RouteObjectInterface::CONTENT_OBJECT] = null;
            }
        } else if ($content instanceof ArticleInterface) {
            $articleMeta = $this->metaLoader->load('article', ['article' => $content]);
            $defaults['type'] = RouteInterface::TYPE_CONTENT;
        }

        if ($articleMeta && $articleMeta->getValues() instanceof ArticleInterface) {
            $defaults[RouteObjectInterface::CONTENT_OBJECT] = $articleMeta->getValues();
        }

        $request->attributes->set('articleMeta', $articleMeta);
        $defaults['_article_meta'] = $articleMeta;

        return $defaults;
    }

    protected function setTemplateName($content, $defaults)
    {
        if ($content) {
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolveFromArticle($content);
        } else {
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolveFromRoute($defaults[RouteObjectInterface::ROUTE_OBJECT]);
        }

        return $defaults;
    }
}
