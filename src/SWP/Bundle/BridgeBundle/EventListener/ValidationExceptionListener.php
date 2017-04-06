<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\BridgeBundle\EventListener;

use SWP\Bundle\BridgeBundle\Exception\ValidationException;
use SWP\Component\Common\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class ValidationExceptionListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ValidationExceptionListener constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $event->setResponse(new Response(
            $this->serializer->serialize($exception->getConstraintViolationList(), $event->getRequest()->getRequestFormat()),
            Response::HTTP_BAD_REQUEST
        ));
    }
}
