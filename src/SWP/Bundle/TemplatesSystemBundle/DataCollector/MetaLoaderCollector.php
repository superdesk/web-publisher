<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\DataCollector;

use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class MetaLoaderCollector extends DataCollector
{
    protected $traceableChainLoader;

    public function __construct(LoaderInterface $traceableChainLoader)
    {
        $this->traceableChainLoader = $traceableChainLoader;
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $data = $this->traceableChainLoader->getData();

        foreach ($data['calledLoaders'] as $class => $loader) {
            $totalDuration = 0;
            foreach ($loader['calls'] as $call) {
                $totalDuration = $totalDuration + $call['duration'];
            }
            $loader['totalDuration'] = $totalDuration;

            $data['calledLoaders'][$class] = $loader;
        }

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'meta_loader_collector';
    }

    public function reset()
    {
        return;
    }
}
