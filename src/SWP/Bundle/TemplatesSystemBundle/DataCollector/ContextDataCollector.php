<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\DataCollector;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ContextDataCollector extends DataCollector
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * ContextDataCollector constructor.
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->data = [
            'currentPage' => $this->context->getCurrentPage() instanceof MetaInterface ?
                $this->getRouteData($this->context->getCurrentPage()->getValues()) :
                [],
            'registeredMeta' => $this->context->getRegisteredMeta(),
        ];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'context_collector';
    }

    /**
     * We don't have anything to reset here.
     */
    public function reset()
    {
        return;
    }

    private function getRouteData(?RouteInterface $route)
    {
        if (null === $route) {
            return null;
        }

        return [
            'title' => $route->getName(),
            'parent' => $this->getRouteData($route->getParent()),
            'templateName' => $route->getTemplateName(),
            'articlesTemplateName' => $route->getArticlesTemplateName(),
            'type' => $route->getType(),
            'cacheTimeInSeconds' => $route->getCacheTimeInSeconds(),
            'slug' => $route->getSlug(),
            'variablePattern' => $route->getVariablePattern(),
            'staticPrefix' => $route->getStaticPrefix(),
            'position' => $route->getPosition(),
        ];
    }
}
