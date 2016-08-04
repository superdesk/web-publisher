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
use SWP\Bundle\ContentBundle\Model\RouteInterface;
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
    private $homePagePaths;

    /**
     * @var array
     */
    private $otherPaths;

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
     * @param array                           $homePagePaths  home page paths
     * @param array                           $otherPaths     other paths
     * @param TenantProviderInterface         $tenantProvider Tenants provider
     * @param TenantAwarePathBuilderInterface $pathBuilder    Path builder
     * @param string                          $siteClass      Site document class
     * @param string                          $documentClass  Page document FQCN
     */
    public function __construct(
        array $homePagePaths,
        array $otherPaths,
        TenantProviderInterface $tenantProvider,
        TenantAwarePathBuilderInterface $pathBuilder,
        $siteClass,
        $documentClass
    ) {
        $this->homePagePaths = $homePagePaths;
        $this->otherPaths = $otherPaths;
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
        $homePagePaths = [];
        $otherPaths = [];
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
                $this->dm->flush();
            }

            foreach ($this->homePagePaths as $path) {
                $homePagePaths[] = $this->pathBuilder->build($path, $subdomain);
            }

            foreach ($this->otherPaths as $path) {
                $otherPaths[] = $this->pathBuilder->build($path, $subdomain);
            }
        }

        $this->dm->flush();

        if (count($homePagePaths) > 0) {
            $this->createHomePageBasePaths($session, $homePagePaths, $tenants);
        }

        if (count($otherPaths) > 0) {
            $this->createOtherBasePaths($session, $otherPaths);
        }
    }

    private function createHomePageBasePaths(SessionInterface $session, array $basePaths, array $tenants)
    {
        $route = null;
        $home = 'homepage';
        foreach ($basePaths as $path) {
            NodeHelper::createPath($session, $path);
            $homepage = $this->dm->find(null, $path.'/'.$home);
            if (null === $homepage) {
                $route = new $this->documentClass();
                $parent = $this->dm->find(null, $path);
                $route->setParentDocument($parent);
                $route->setName($home);
                $route->setType(RouteInterface::TYPE_CONTENT);
                $this->dm->persist($route);
                $this->dm->flush();
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

    private function createOtherBasePaths(SessionInterface $session, array $basePaths)
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
