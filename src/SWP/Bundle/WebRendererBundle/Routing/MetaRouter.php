<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Routing;

use SWP\Bundle\ContentBundle\Document\Article;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

class MetaRouter extends DynamicRouter
{
    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = false)
    {
        $route = $name;
        if ($name instanceof Meta && $name->getValues() instanceof Article) {
            $parameters['slug'] = $name->getValues()->getSlug();
            $route = $name->getValues()->getRoute();
        }

        return parent::generate($route, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name instanceof Meta;
    }
}
