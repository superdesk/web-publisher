<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RouteInterface;

class PublishDestination implements PublishDestinationInterface
{
    /**
     * @var TenantInterface
     */
    private $tenant;

    /**
     * @var RouteInterface
     */
    private $route;

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }
}
