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

namespace SWP\Bundle\TemplatesSystemBundle\Loader;

use SWP\Component\TemplatesSystem\Gimme\Loader\ChainLoader;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TraceableChainLoader extends ChainLoader
{
    private $data = [
        'calledLoaders' => [],
        'loaders' => [],
        'totalCalls' => 0,
        'totalDuration' => 0,
    ];

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        foreach ($this->loaders as $priority => $loader) {
            $loaderClass = \get_class($loader);
            if (!\array_key_exists($loaderClass, $this->data['loaders'])) {
                $this->data['loaders'][$loaderClass] = [
                    'priority' => $priority,
                    'calls' => [],
                ];
            }
        }

        foreach ($this->loaders as $priority => $loader) {
            $loaderClass = \get_class($loader);
            $stopwatch = new Stopwatch();
            if ($loader->isSupported($type)) {
                if (!\array_key_exists($loaderClass, $this->data['calledLoaders'])) {
                    $this->data['calledLoaders'][$loaderClass] = [
                        'priority' => $priority,
                        'calls' => [],
                    ];
                }

                $stopwatch->start($loaderClass);
                $meta = $loader->load($type, $parameters, $withoutParameters, $responseType);
                $event = $stopwatch->stop($loaderClass);

                $this->data['calledLoaders'][$loaderClass]['calls'][] = [
                    'type' => $type,
                    'parameters' => json_encode($parameters, JSON_THROW_ON_ERROR, 512),
                    'withoutParameters' => json_encode($withoutParameters, JSON_THROW_ON_ERROR, 512),
                    'responseType' => $responseType,
                    'duration' => $event->getDuration(),
                    'found' => false !== $meta,
                ];
                ++$this->data['totalCalls'];
                $this->data['totalDuration'] = $this->data['totalDuration'] + $event->getDuration();

                if (false !== $meta) {
                    return $meta;
                }
            }
        }

        return false;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
