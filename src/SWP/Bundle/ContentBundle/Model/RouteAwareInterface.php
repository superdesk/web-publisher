<?php

namespace SWP\Bundle\ContentBundle\Model;

interface RouteAwareInterface
{
    /**
     * @return RouteInterface|null
     */
    public function getRoute();

    /**
     * @param RouteInterface|null $route
     */
    public function setRoute(RouteInterface $route = null);

    /**
     * @return int|null
     */
    public function getRouteId();
}
