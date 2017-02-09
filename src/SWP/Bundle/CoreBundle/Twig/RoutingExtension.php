<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Twig;

use Symfony\Bridge\Twig\Extension\RoutingExtension as SymfonyRoutingExtension;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RoutingExtension extends SymfonyRoutingExtension
{
    /**
     * {@inheritdoc}
     */
    public function getPath($name, $parameters = array(), $relative = false)
    {
        try {
            return parent::getPath($name, $parameters, $relative);
        } catch (RouteNotFoundException $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($name, $parameters = array(), $schemeRelative = false)
    {
        try {
            return parent::getUrl($name, $parameters, $schemeRelative);
        } catch (RouteNotFoundException $e) {
        }
    }
}
