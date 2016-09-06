<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Routing;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class MetaRouter extends DynamicRouter
{
    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = false)
    {
        $route = $name;
        if ($name->getValues() instanceof ArticleInterface) {
            $parameters['slug'] = $name->getValues()->getSlug();
            $route = $name->getValues()->getRoute();
        } elseif ($name->getValues() instanceof RouteInterface) {
            $route = $name->getValues();
        }

        return parent::generate($route, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name instanceof Meta && (
            $name->getValues() instanceof ArticleInterface ||
            $name->getValues() instanceof RouteInterface
        );
    }
}
