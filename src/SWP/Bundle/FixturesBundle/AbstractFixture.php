<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Nelmio\Alice\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture as BaseFixture;

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
        Fixtures::load($this->locateResources($paths), $manager, $parameters);
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
