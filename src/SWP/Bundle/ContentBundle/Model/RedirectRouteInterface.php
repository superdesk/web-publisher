<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Bundle\RedirectRouteBundle\Model\RedirectRouteInterface as BaseRedirectRouteInterface;

interface RedirectRouteInterface extends BaseRedirectRouteInterface
{
    public function getRouteSource(): ?RouteInterface;

    public function setRouteSource(?RouteInterface $route): void;
}
