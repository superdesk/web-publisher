<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates as BasePrefixCandidates;

/**
 * Class PrefixCandidates.
 */
class PrefixCandidates extends BasePrefixCandidates
{
    /**
     * @var TenantAwarePathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var array
     */
    protected $routePathsNames = [];

    /**
     * {@inheritdoc}
     */
    public function getPrefixes()
    {
        $this->idPrefixes = (array) $this->pathBuilder->build($this->routePathsNames);

        return $this->idPrefixes;
    }

    /**
     * Sets path builder.
     *
     * @param TenantAwarePathBuilderInterface $pathBuilder
     */
    public function setPathBuilder(TenantAwarePathBuilderInterface $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * @param array $routePathsNames
     */
    public function setRoutePathsNames(array $routePathsNames = [])
    {
        $this->routePathsNames = $routePathsNames;
    }
}
