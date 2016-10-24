<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle;

use Doctrine\Common\DataFixtures\AbstractFixture as BaseFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract fixture class.
 */
abstract class AbstractFixture extends BaseFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get current kernel environment.
     *
     * @return string Environment type
     */
    public function getEnvironment()
    {
        return $this->container->get('kernel')->getEnvironment();
    }

    /**
     * Loads Alice fixtures.
     *
     * @param array|string  $paths      Fixtures path(s)
     * @param ObjectManager $manager    Object manager
     * @param array         $parameters Extra parameters
     */
    public function loadFixtures($paths, $manager, $parameters = [])
    {
        return Fixtures::load($this->locateResources($paths), $manager, $parameters);
    }

    /**
     * Finds the PHPCR node by given id/path.
     *
     * @param string|null $className Document class name
     * @param string      $id        PHPCR path
     *
     * @return string|null
     */
    public function find($className, $id)
    {
        return $this->container->get('document_manager')->find($className, $id);
    }

    /**
     * Finds the PHPCR node by given id/path.
     *
     * @param string|null $className Document class name
     * @param string      $id        PHPCR path
     *
     * @return string|null
     */
    public function findByTenant($className, $id)
    {
        return $this->find($className, $this->generatePath($id));
    }

    /**
     * Generates tenant aware path.
     *
     * @param string $id
     *
     * @return string
     */
    public function generatePath($id)
    {
        return $this->getTenantPrefix().'/'.ltrim($id, '/');
    }

    public function getRouteByName($id)
    {
        return $this->container->get('swp.provider.route')->getRouteByName($id);
    }

    /**
     * Gets current tenant's prefix.
     *
     * @param string $subdomain
     *
     * @return string
     */
    public function getTenantPrefix($subdomain = TenantInterface::DEFAULT_TENANT_SUBDOMAIN)
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->container->get('swp.repository.tenant')->findOneBySubdomain($subdomain);

        if (null === $tenant) {
            throw new TenantNotFoundException($subdomain);
        }

        return $tenant->getId();
    }

    /**
     * Locates the fixtures resources.
     *
     * @param array|string $paths Fixtures path(s)
     *
     * @return array|string the path(s)
     */
    protected function locateResources($paths)
    {
        $kernel = $this->container->get('kernel');
        if (is_array($paths)) {
            foreach ($paths as $key => $path) {
                $paths[$key] = $kernel->locateResource($path);
            }
        } else {
            $paths = $kernel->locateResource($paths);
        }

        return $paths;
    }
}
