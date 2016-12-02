<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\BridgeBundle\Tests\Functional\app;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $testCase;
    private $rootConfig;

    public function __construct($testCase, $rootConfig, $environment, $debug)
    {
        if (!is_dir(__DIR__.'/'.$testCase)) {
            throw new \InvalidArgumentException(sprintf('The test case "%s" does not exist.', $testCase));
        }

        $this->testCase = $testCase;
        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($rootConfig) && !file_exists($rootConfig = __DIR__.'/'.$testCase.'/'.$rootConfig)) {
            throw new \InvalidArgumentException(sprintf('The root config "%s" does not exist.', $rootConfig));
        }

        $this->rootConfig = $rootConfig;
        parent::__construct($environment, $debug);
    }

    public function registerBundles()
    {
        if (!file_exists($filename = $this->getRootDir().'/'.$this->testCase.'/bundles.php')) {
            throw new \RuntimeException(sprintf('The bundles file "%s" does not exist.', $filename));
        }

        return include $filename;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->rootConfig);
    }

    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.test_case'] = $this->testCase;

        return $parameters;
    }
}
