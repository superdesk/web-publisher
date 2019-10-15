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

namespace SWP\Bundle\CoreBundle\Enhancer;

use SWP\Component\Common\Exception\ArticleNotFoundException;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
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
    public const ARTICLE_META = '_article_meta';

    public const ROUTE_META = '_route_meta';

    protected $templateNameResolver;

    protected $metaLoader;

    protected $context;

    private $enhancedRoutesDefaults = [];

    public function __construct(TemplateNameResolverInterface $templateNameResolver, LoaderInterface $metaLoader, Context $context)
    {
        $this->templateNameResolver = $templateNameResolver;
        $this->metaLoader = $metaLoader;
        $this->context = $context;
    }

    public function enhance(array $defaults, Request $request): array
    {
        $defaultsKey = md5(json_encode($defaults));
        if (!isset($this->enhancedRoutesDefaults[$defaultsKey])) {
            $route = $defaults[RouteObjectInterface::ROUTE_OBJECT];

            if (!isset($defaults['_controller']) || (isset($defaults['_controller']) && sprintf('%s::urlRedirectAction', RedirectController::class) !== $defaults['_controller'])) {
                $defaults['_controller'] = ContentController::class.'::renderPageAction';
            }

            $defaults = $this->setRouteMeta($defaults);
            $request->attributes->set('routeMeta', $defaults[self::ROUTE_META]);

            $defaults = $this->setArticleMeta($this->getContentFromDefaults($defaults), $defaults);
            $request->attributes->set('articleMeta', $defaults[self::ARTICLE_META]);

            $defaults = $this->setTemplateName($this->getContentFromDefaults($defaults), $defaults);

            if (null !== ($article = $this->getContentFromDefaults($defaults)) && !isset($defaults['slug']) && RouteInterface::TYPE_CONTENT === $route->getType()) {
                $defaults['slug'] = $article->getSlug();
            }
            $this->enhancedRoutesDefaults[$defaultsKey] = $defaults;
        } else {
            $defaults = $this->enhancedRoutesDefaults[$defaultsKey];
        }

        return $defaults;
    }

    public function setArticleMeta($content, array $defaults): array
    {
        $articleMeta = false;
        if (isset($defaults['slug'])) {
            $articleMeta = $this->metaLoader->load('article', ['slug' => $defaults['slug']], LoaderInterface::SINGLE);
            $defaults['type'] = RouteInterface::TYPE_COLLECTION;
            if (false === $articleMeta || ($articleMeta->getValues()->getRoute()->getId() !== $defaults[RouteObjectInterface::ROUTE_OBJECT]->getId())) {
                throw new ArticleNotFoundException(sprintf('Article for slug: %s was not found.', $defaults['slug']));
            }
        } elseif ($content instanceof ArticleInterface) {
            $articleMeta = $this->metaLoader->load('article', ['article' => $content], LoaderInterface::SINGLE);
            $defaults['type'] = RouteInterface::TYPE_CONTENT;
            if (false === $articleMeta) {
                throw new ArticleNotFoundException(sprintf('Content with id: %s was not found.', $content->getId()));
            }
        }
        if ($articleMeta && $articleMeta->getValues() instanceof ArticleInterface) {
            $defaults[RouteObjectInterface::CONTENT_OBJECT] = $articleMeta->getValues();
        }

        $defaults[self::ARTICLE_META] = $articleMeta;

        return $defaults;
    }

    public function setTemplateName($content, array $defaults): array
    {
        $route = $defaults[RouteObjectInterface::ROUTE_OBJECT] ?? null;
        if ($content && isset($defaults['slug'])) {
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolve($content);
        } elseif (null !== $route) {
            $defaults[RouteObjectInterface::TEMPLATE_NAME] = $this->templateNameResolver->resolve($route);
        }

        return $defaults;
    }

    public function setRouteMeta(array $defaults): array
    {
        $routeMeta = $this->metaLoader->load('route', ['route_object' => $defaults[RouteObjectInterface::ROUTE_OBJECT]]);
        $defaults[self::ROUTE_META] = $routeMeta;

        if ($routeMeta instanceof Meta) {
            $this->context->setCurrentPage($routeMeta);
        }

        return $defaults;
    }

    private function getContentFromDefaults(array $defaults)
    {
        if (isset($defaults[RouteObjectInterface::CONTENT_OBJECT])) {
            return $defaults[RouteObjectInterface::CONTENT_OBJECT];
        }
    }
}
