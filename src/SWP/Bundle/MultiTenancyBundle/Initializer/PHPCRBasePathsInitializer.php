<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Initializer;

use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\PHPCR\Query\Query;
use PHPCR\SessionInterface;
use PHPCR\Util\NodeHelper;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use SWP\Component\MultiTenancy\Provider\TenantProviderInterface;

/**
 * PHPCR Base Paths Repository Initializer.
 *
 * It creates based paths in content repository based on provided
 * tenants and config. Disabled by default, can be enabled in config.
 * Requires DoctrinePHPCRBundle to be configured in the system.
 */
class PHPCRBasePathsInitializer implements InitializerInterface
{
    /**
     * @var array
     */
    private $paths;

    /**
     * @var TenantProviderInterface
     */
    private $tenantProvider;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    private $pathBuilder;

    /**
     * Construct.
     *
     * @param array                           $paths          Content paths
     * @param TenantProviderInterface         $tenantProvider Tenants provider
     * @param TenantAwarePathBuilderInterface $pathBuilder    Path builder
     */
    public function __construct(
        array $paths,
        TenantProviderInterface $tenantProvider,
        TenantAwarePathBuilderInterface $pathBuilder
    ) {
        $this->paths = $paths;
        $this->tenantProvider = $tenantProvider;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function init(ManagerRegistry $registry)
    {
        /** @var SessionInterface $session */
        $session = $registry->getConnection();

        /** @var Query $tenantsQuery */
        $tenantsQuery = $this->tenantProvider->getAvailableTenants();
        /** @var Collection $tenants */
        $tenants = $tenantsQuery->getResult();

        $this->generateBasePaths($session, $tenants->toArray());
    }

    private function generateBasePaths(SessionInterface $session, array $tenants = [])
    {
        $basePaths = [];

        foreach ($tenants as $tenant) {
            foreach ($this->paths as $path) {
                $basePaths[] = $this->pathBuilder->build(
                    $path,
                    $tenant->getOrganization()->getCode().'/'.$tenant->getCode()
                );
            }
        }

        if (count($basePaths) > 0) {
            $this->createBasePaths($session, $basePaths);
        }
    }

    private function createBasePaths(SessionInterface $session, array $basePaths)
    {
        foreach ($basePaths as $path) {
            NodeHelper::createPath($session, $path);
        }

        $session->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Multi-tenancy base paths';
    }
}
