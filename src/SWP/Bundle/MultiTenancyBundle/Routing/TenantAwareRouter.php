<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Routing;

use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;

class TenantAwareRouter extends DynamicRouter
{
    /**
     * @var TenantAwarePathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = false)
    {
        if (null === $name && isset($parameters['content_id'])) {
            $contentId = $this->checkAndRemoveFirstSlash($parameters['content_id']);
            $parameters['content_id'] = $this->pathBuilder->build('/', $contentId);
        }

        if (is_string($name)) {
            $name = (string) $this->pathBuilder->build(
                $this->checkAndRemoveFirstSlash($name)
            );
        }

        return parent::generate($name, $parameters, $referenceType);
    }

    private function checkAndRemoveFirstSlash($string)
    {
        if (substr($string, 0, 1) === '/') {
            return substr($string, 1);
        }

        return $string;
    }

    /**
     * Sets the tenant aware path builder.
     *
     * @param TenantAwarePathBuilderInterface $pathBuilder Path builder
     */
    public function setPathBuilder(TenantAwarePathBuilderInterface $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }
}
