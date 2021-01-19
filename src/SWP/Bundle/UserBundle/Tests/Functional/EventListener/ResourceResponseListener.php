<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Tests\Functional\EventListener;

use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class ResourceResponseListener
{
    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        /** @var ResponseContext $responseContext */
        $responseContext = $controllerResult->getResponseContext();
        if ($controllerResult instanceof ResourcesListResponseInterface) {
            $event->setResponse(new JsonResponse($controllerResult->getResources(), $responseContext->getStatusCode()));
        } elseif ($controllerResult instanceof SingleResourceResponseInterface) {
            if ($controllerResult->getResource() instanceof FormInterface) {
                $errors = array();
                foreach ($controllerResult->getResource()->getErrors(true) as $error) {
                    $errors[] = array('message' => $error->getMessage());
                }

                $event->setResponse(new Response($this->getSerializer()->serialize($errors, 'json'), $responseContext->getStatusCode()));

                return;
            }

            $event->setResponse(new Response($this->getSerializer()->serialize($controllerResult->getResource(), 'json'), $responseContext->getStatusCode()));
        }
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer(): SerializerInterface
    {
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizers = [$normalizer];

        return new Serializer($normalizers, $encoders);
    }
}
