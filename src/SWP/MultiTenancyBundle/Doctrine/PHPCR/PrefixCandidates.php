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
namespace SWP\MultiTenancyBundle\Doctrine\PHPCR;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates as BasePrefixCandidates;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;

class PrefixCandidates extends BasePrefixCandidates
{
    protected $pathBuilder;

    public function __construct(
        TenantAwarePathBuilderInterface $pathBuilder,
        array $locales = array(),
        ManagerRegistry $doctrine = null,
        $limit = 20
    ) {
        $this->pathBuilder = $pathBuilder;
        parent::__construct($this->resolvePrefixes(), $locales, $doctrine, $limit);
    }

    protected function resolvePrefixes()
    {
        return $this->pathBuilder->build(['routes', 'content']);
    }
}
