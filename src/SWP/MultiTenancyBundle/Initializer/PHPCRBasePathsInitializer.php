<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Initializer;

use PHPCR\Util\NodeHelper;
use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use PHPCR\SessionInterface;
use SWP\MultiTenancyBundle\Provider\TenantProviderInterface;

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
     * @var string
     */
    private $rootPath;

    /**
     * @var string|null
     */
    private $cnd;

    /**
     * Construct.
     *
     * @param array                   $paths          Content paths
     * @param TenantProviderInterface $tenantProvider Tenants provider
     * @param string                  $rootPath       Root path
     * @param string|null             $cnd            Node type and namespace definitions in cnd
     *                                                format, pass null to not create any node types.
     */
    public function __construct(array $paths, TenantProviderInterface $tenantProvider, $rootPath, $cnd = null)
    {
        $this->paths = $paths;
        $this->tenantProvider = $tenantProvider;
        $this->rootPath = $rootPath;
        $this->cnd = $cnd;
    }

    /**
     * {@inheritdoc}
     */
    public function init(ManagerRegistry $registry)
    {
        $session = $registry->getConnection();

        if ($this->cnd) {
            $this->registerCnd($session, $this->cnd);
        }

        $basePaths = $this->getBasePaths();

        if (count($basePaths)) {
            $this->createBasePaths($session, $basePaths);
        }
    }

    private function registerCnd(SessionInterface $session, $cnd)
    {
        $session->getWorkspace()->getNodeTypeManager()->registerNodeTypesCnd($cnd, true);
    }

    private function getBasePaths()
    {
        $tenants = $this->tenantProvider->getAvailableTenants();

        return $this->genereteBasePaths($tenants);
    }

    private function genereteBasePaths(array $tenants = array())
    {
        foreach ($tenants as $tenant) {
            foreach ($this->paths as $path) {
                $basePaths[] = $this->rootPath.DIRECTORY_SEPARATOR.$tenant['subdomain'].DIRECTORY_SEPARATOR.$path;
            }
        }

        return $basePaths;
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
        return 'SWPMultiTenancyBundle base paths';
    }
}
