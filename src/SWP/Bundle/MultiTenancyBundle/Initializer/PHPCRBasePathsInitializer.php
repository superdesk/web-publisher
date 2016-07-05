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
namespace SWP\Bundle\MultiTenancyBundle\Initializer;

use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use PHPCR\SessionInterface;
use PHPCR\Util\NodeHelper;
use SWP\Component\MultiTenancy\Model\SiteDocumentInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use SWP\Component\MultiTenancy\Provider\TenantProviderInterface;
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
     * @var TenantAwarePathBuilderInterface
     */
    private $pathBuilder;

    /**
     * @var string
     */
    private $siteClass;

    /**
     * @var string
     */
    private $documentClass;

    /**
     * Construct.
     *
     * @param array                           $paths          Content paths
     * @param TenantProviderInterface         $tenantProvider Tenants provider
     * @param TenantAwarePathBuilderInterface $pathBuilder    Path builder
     * @param string                          $siteClass      Site document class
     * @param string                          $documentClass  Page document FQCN
     */
    public function __construct(
        array $paths,
        TenantProviderInterface $tenantProvider,
        TenantAwarePathBuilderInterface $pathBuilder,
        $siteClass,
        $documentClass
    ) {
        $this->paths = $paths;
        $this->tenantProvider = $tenantProvider;
        $this->pathBuilder = $pathBuilder;
        $this->siteClass = $siteClass;
        $this->documentClass = $documentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function init(ManagerRegistry $registry)
    {
        $session = $registry->getConnection();
        $this->dm = $registry->getManager();
        $tenants = $this->tenantProvider->getAvailableTenants();

        $this->generateBasePaths($session, $tenants);
        $this->dm->flush();
    }

    private function generateBasePaths(SessionInterface $session, array $tenants = [])
    {
        $basePaths = [];
        foreach ($tenants as $tenant) {
            $subdomain = $tenant['subdomain'];
            $site = $this->dm->find($this->siteClass, $this->pathBuilder->build('/', $subdomain));
            if (!$site) {
                $site = new $this->siteClass();
                if (!$site instanceof SiteDocumentInterface) {
                    throw new UnexpectedTypeException($site, 'SWP\Component\MultiTenancy\Model\SiteDocumentInterface');
                }

                $site->setId((string) $this->pathBuilder->build('/', $subdomain));
                $this->dm->persist($site);
            }

            foreach ($this->paths as $path) {
                $basePaths[] = $this->pathBuilder->build($path, $subdomain);
            }
        }

        $this->dm->flush();

        if (count($basePaths) > 0) {
            $this->createBasePaths($session, $basePaths, $tenants);
        }
    }

    private function createBasePaths(SessionInterface $session, array $basePaths, array $tenants)
    {
        $route = null;
        $home = 'homepage';
        foreach ($basePaths as $path) {
            NodeHelper::createPath($session, $path);
            $homepage = $this->dm->find(null, $path.'/'.$home);
            if (null === $homepage) {
                $route = new $this->documentClass();
                $route->setParentDocument($this->dm->find(null, $path));
                $route->setName($home);
                $this->dm->persist($route);
            }
        }

        $session->save();
        foreach ($tenants as $tenant) {
            $site = $this->dm->find($this->siteClass, $this->pathBuilder->build('/', $tenant['subdomain']));
            if (null !== $site && null === $site->getHomepage()) {
                $site->setHomepage($route);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Multi-tenancy base paths';
    }
}
