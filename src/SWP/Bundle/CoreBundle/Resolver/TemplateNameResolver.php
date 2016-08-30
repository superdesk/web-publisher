<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * Some parts of that file were taken from the Liip/ThemeBundle
 * (c) Liip AG
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Resolver;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TemplateNameResolver implements TemplateNameResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($object, $defaultFileName = 'article.html.twig')
    {
        if ($object instanceof ArticleInterface) {
            return $this->resolveFromArticle($object, $defaultFileName);
        } elseif ($object instanceof RouteInterface) {
            return $this->resolveFromRoute($object, $defaultFileName);
        }

        return $defaultFileName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFromArticle(ArticleInterface $article, $default = 'article.html.twig')
    {
        $templateName = $default;
        if (null !== ($route = $article->getRoute())) {
            if (RouteInterface::TYPE_COLLECTION === $route->getType()) {
                return $templateName;
            }

            $routeTemplateName = $this->resolveFromRoute($route, false);

            if (false !== $routeTemplateName) {
                $templateName = $routeTemplateName;
            }
        }

        if (null !== ($articleTemplateName = $article->getTemplateName())) {
            return $articleTemplateName;
        }

        return $templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFromRoute(RouteInterface $route, $default = 'article.html.twig')
    {
        if (null !== ($templateName = $route->getTemplateName())) {
            return $templateName;
        }

        if (RouteInterface::TYPE_COLLECTION === $route->getType()) {
            throw new NotFoundHttpException(sprintf('There is no template file defined for "%s" route!', $route->getId()));
        }

        return $default;
    }
}
