<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\CoreBundle\Enhancer\RouteEnhancer;
use SWP\Bundle\CoreBundle\GeoIp\CachedGeoIpChecker;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class GeoIPSubscriber implements EventSubscriberInterface
{
    /** @var CachedGeoIpChecker */
    private $geoIpChecker;

    /** @var bool */
    private $isGeoIpEnabled;

    public function __construct(CachedGeoIpChecker $geoIpChecker, bool $isGeoIPEnabled = false)
    {
        $this->geoIpChecker = $geoIpChecker;
        $this->isGeoIpEnabled = $isGeoIPEnabled;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 1],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === $this->isGeoIpEnabled || HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if (!($content = $request->attributes->get(RouteEnhancer::ARTICLE_META)) instanceof Meta) {
            return;
        }

        $object = $content->getValues();

        if (!$object instanceof ArticleInterface) {
            return;
        }

        if (null !== $content && !$this->geoIpChecker->isGranted($request->getClientIp(), $object)) {
            throw new AccessDeniedHttpException('Access denied');
        }
    }
}
