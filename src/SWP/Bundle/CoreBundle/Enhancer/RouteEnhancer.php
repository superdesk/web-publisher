<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Enhancer;

use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Bundle\CoreBundle\Resolver\TemplateNameResolverInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Controller\ContentController;

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
     * Adjust route defaults and request attributes to our needs.
     *
     * @param array   $defaults
     * @param Request $request
     *
     * @return array
     */
    public function enhance(array $defaults, Request $request)
    {
        $defaults['_controller'] = ContentController::class.'::renderPageAction';
        $defaults = $this->setArticleMeta($this->getContentFromDefaults($defaults), $request, $defaults);
        $defaults = $this->setTemplateName($this->getContentFromDefaults($defaults), $defaults);

        return $defaults;
    }

    /**
     * Get article based on available parameters, set route type.
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
            $articleMeta = $this->metaLoader->load('article', ['slug' => $defaults['slug']], LoaderInterface::SINGLE);
            $defaults['type'] = RouteInterface::TYPE_COLLECTION;
            if (null === $articleMeta) {
                $defaults[RouteObjectInterface::CONTENT_OBJECT] = null;
            }
        } elseif ($content instanceof ArticleInterface) {
            $articleMeta = $this->metaLoader->load('article', ['article' => $content], LoaderInterface::SINGLE);
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
     * Resolve template name based on available data.
     *
     * @param mixed $content
     * @param array $defaults
     *
     * @return array
     */
    public function setTemplateName($content, array $defaults)
    {
        if ($content) {
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolve($content);
        } else {
            $route = isset($defaults[RouteObjectInterface::ROUTE_OBJECT]) ? $defaults[RouteObjectInterface::ROUTE_OBJECT] : null;
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolve($route);
        }

        return $defaults;
    }

    private function getContentFromDefaults($defaults)
    {
        if (isset($defaults[RouteObjectInterface::CONTENT_OBJECT])) {
            return $defaults[RouteObjectInterface::CONTENT_OBJECT];
        }

        return;
    }
}
