<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Fragment;

use SWP\Bundle\BridgeBundle\Client\GuzzleClient;
use SWP\Bundle\BridgeBundle\Exception\ClientException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExternalFragmentRenderer.
 *
 * Render content fetched from external uri with guzzle.
 */
class ExternalFragmentRenderer implements FragmentRendererInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param HttpKernelInterface      $kernel     A HttpKernelInterface instance
     * @param EventDispatcherInterface $dispatcher A EventDispatcherInterface instance
     */
    public function __construct(HttpKernelInterface $kernel, EventDispatcherInterface $dispatcher = null)
    {
        $this->kernel = $kernel;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function render($uri, Request $request, array $options = [])
    {
        $level = ob_get_level();

        try {
            return new Response($this->createExternalRequest($uri));
        } catch (\Throwable $e) {
            // we dispatch the exception event to trigger the logging
            // the response that comes back is simply ignored
            if (isset($options['ignore_errors']) && $options['ignore_errors'] && $this->dispatcher) {
                $event = new ExceptionEvent($this->kernel, $request, HttpKernelInterface::SUB_REQUEST, $e);

                $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);
            }

            // let's clean up the output buffers that were created by the sub-request
            Response::closeOutputBuffers($level, false);

            if (isset($options['alt'])) {
                $alt = $options['alt'];
                unset($options['alt']);

                return $this->render($alt, $request, $options);
            }

            if (!isset($options['ignore_errors']) || !$options['ignore_errors']) {
                throw $e;
            }

            return new Response();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'external';
    }

    /**
     * @throws ClientException
     */
    private function createExternalRequest(string $uri): string
    {
        $client = new GuzzleClient();

        return $client->makeCall($uri)['body'];
    }
}
