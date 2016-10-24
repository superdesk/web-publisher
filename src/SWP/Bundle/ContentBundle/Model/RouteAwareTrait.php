<?php

namespace SWP\Bundle\ContentBundle\Model;

trait RouteAwareTrait
{
    /**
     * @var RouteInterface|null
     */
    protected $route;

    /**
     * @return RouteInterface|null
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route = null)
    {
        $this->route = $route;
    }
}
