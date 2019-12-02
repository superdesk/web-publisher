<?php

declare(strict_types=1);

namespace SWP\Bundle\GeoIPBundle\EventSubscriber;

use SWP\Bundle\CoreBundle\Enhancer\RouteEnhancer;
use SWP\Bundle\GeoIPBundle\Checker\GeoIPChecker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class GeoIPSubscriber implements EventSubscriberInterface
{
    private $geoIpChecker;

    public function __construct(GeoIPChecker $geoIpChecker)
    {
        $this->geoIpChecker = $geoIpChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 1],
            ],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $content = $request->attributes->get(RouteEnhancer::ARTICLE_META);

        if (null !== $content && $this->geoIpChecker->isGranted($request->getClientIp(), $content->getValues())) {
            $request->attributes->set('_geo_ip_is_granted', false);
            throw new AccessDeniedHttpException('Access denied');
        }
    }
}
