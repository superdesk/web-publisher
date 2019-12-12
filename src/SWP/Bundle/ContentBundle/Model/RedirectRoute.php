<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Bundle\RedirectRouteBundle\Model\RedirectRoute as BaseRedirectRoute;

class RedirectRoute extends BaseRedirectRoute implements RedirectRouteInterface
{
    /** @var RouteInterface|null */
    protected $routeSource;

    public function getRouteSource(): ?RouteInterface
    {
        return $this->routeSource;
    }

    public function setRouteSource(?RouteInterface $route): void
    {
        $this->routeSource = $route;
    }
}
