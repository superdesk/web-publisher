<?php

/*
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
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Resolver;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TemplateNameResolver.
 */
class TemplateNameResolver implements TemplateNameResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($object, $defaultFileName = TemplateNameResolverInterface::TEMPLATE_NAME)
    {
        if ($object instanceof ArticleInterface) {
            return $this->resolveFromArticle($object);
        } elseif ($object instanceof RouteInterface) {
            return $this->resolveFromRoute($object);
        }

        return $defaultFileName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFromArticle(ArticleInterface $article, $templateName = TemplateNameResolverInterface::TEMPLATE_NAME)
    {
        /** @param $route RouteInterface */
        if (null !== ($route = $article->getRoute())) {
            if (null !== $route->getTemplateName()) {
                $templateName = $route->getTemplateName();
            }

            if (RouteInterface::TYPE_COLLECTION === $route->getType() && null !== $route->getArticlesTemplateName()) {
                $templateName = $route->getArticlesTemplateName();
            }
        }

        if (null !== $article->getTemplateName()) {
            $templateName = $article->getTemplateName();
        }

        return $templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFromRoute(RouteInterface $route, $templateName = TemplateNameResolverInterface::TEMPLATE_NAME)
    {
        if (null !== $route->getTemplateName()) {
            $templateName = $route->getTemplateName();
        }

        if (RouteInterface::TYPE_COLLECTION === $route->getType() && null === $route->getTemplateName()) {
            if ($contentTemplateName = $this->getTemplateNameFromRouteContent($route)) {
                $templateName = $contentTemplateName;
            } else {
                throw new NotFoundHttpException(sprintf('There is no template file defined for "%s" route!', $route->getName()));
            }
        } elseif (RouteInterface::TYPE_CONTENT === $route->getType()) {
            if ($contentTemplateName = $this->getTemplateNameFromRouteContent($route)) {
                $templateName = $contentTemplateName;
            }
        }

        return $templateName;
    }

    /**
     * @param RouteInterface $route
     *
     * @return bool
     */
    private function getTemplateNameFromRouteContent(RouteInterface $route)
    {
        if (null !== $route->getContent()) {
            if (null !== $templateName = $route->getContent()->getTemplateName()) {
                return $templateName;
            }
        }

        return false;
    }
}
