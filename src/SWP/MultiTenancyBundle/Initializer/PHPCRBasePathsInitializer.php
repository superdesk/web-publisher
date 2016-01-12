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
use SWP\MultiTenancyBundle\Doctrine\PHPCR\TenantAwarePathBuilderInterface;
use SWP\MultiTenancyBundle\Document\SiteDocumentInterface;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

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
    private $pathBuilder;

    /**
     * @var string
     */
    private $siteClass;

    /**
     * Construct.
     *
     * @param array                           $paths          Content paths
     * @param TenantProviderInterface         $tenantProvider Tenants provider
     * @param TenantAwarePathBuilderInterface $pathBuilder    Path builder
     * @param string                          $siteClass      Site document class
     */
    public function __construct(
        array $paths,
        TenantProviderInterface $tenantProvider,
        TenantAwarePathBuilderInterface $pathBuilder,
        $siteClass
    ) {
        $this->paths = $paths;
        $this->tenantProvider = $tenantProvider;
        $this->pathBuilder = $pathBuilder;
        $this->siteClass = $siteClass;
    }

    /**
     * {@inheritdoc}
     */
    public function init(ManagerRegistry $registry)
    {
        $session = $registry->getConnection();
        $this->dm = $registry->getManager();

        $basePaths = $this->getBasePaths();
        $this->dm->flush();
        if (count($basePaths)) {
            $this->createBasePaths($session, $basePaths);
        }
    }

    private function getBasePaths()
    {
        $tenants = $this->tenantProvider->getAvailableTenants();

        return $this->genereteBasePaths($tenants);
    }

    private function genereteBasePaths(array $tenants = array())
    {
        $basePaths = array();
        foreach ($tenants as $tenant) {
            foreach ($this->paths as $path) {
                $basePaths[] = $this->pathBuilder->build($path, $tenant['subdomain']);
            }

            $site = new $this->siteClass();
            if (!$site instanceof SiteDocumentInterface) {
                throw new UnexpectedTypeException($site, 'SWP\MultiTenancyBundle\Document\SiteDocumentInterface');
            }

            $site->setId($this->pathBuilder->build('/', $tenant['subdomain']));
            $this->dm->persist($site);
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
