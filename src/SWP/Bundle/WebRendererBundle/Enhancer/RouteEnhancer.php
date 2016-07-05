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
    /**
     * @var TemplateNameResolverInterface
     */
    protected $templateNameResolver;

    /**
     * @var LoaderInterface
     */
    protected $metaLoader;

    /**
     * @param TemplateNameResolverInterface $templateNameResolver
     * @param LoaderInterface               $metaLoader
     */
    public function __construct(TemplateNameResolverInterface $templateNameResolver, LoaderInterface $metaLoader)
    {
        $this->templateNameResolver = $templateNameResolver;
        $this->metaLoader = $metaLoader;
    }

    /**
     * Adjust route defaults and request attributes to our needs
     *
     * @param array   $defaults
     * @param Request $request
     *
     * @return array
     */
    public function enhance(array $defaults, Request $request)
    {
        $content = $defaults[RouteObjectInterface::CONTENT_OBJECT];
        $defaults['_controller'] = ContentController::class . '::renderPageAction';
        $defaults = $this->setArticleMeta($content, $request, $defaults);
        $defaults = $this->setTemplateName($content, $defaults);

        return $defaults;
    }

    /**
     * Get article based on available parameters, set route type
     *
     * @param mixed   $content
     * @param Request $request
     * @param array   $defaults
     *
     * @return array
     */
    public function setArticleMeta($content, Request $request, array $defaults)
    {
        $articleMeta = null;
        if (isset($defaults['slug'])) {
            $articleMeta = $this->metaLoader->load('article', ['slug' => $defaults['slug']]);
            $defaults['type'] = RouteInterface::TYPE_COLLECTION;
            if (null == $articleMeta) {
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

    /**
     * Resolve template name based on available data
     * @param mixed  $content
     * @param array  $defaults
     *
     * @return array
     */
    public function setTemplateName($content, array $defaults)
    {
        if ($content) {
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolve($content);
        } else {
            $route = isset($defaults[RouteObjectInterface::ROUTE_OBJECT])? $defaults[RouteObjectInterface::ROUTE_OBJECT] : null;
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolve($route);
        }

        return $defaults;
    }
}
