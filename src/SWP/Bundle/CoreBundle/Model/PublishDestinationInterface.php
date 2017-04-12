<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;

interface PublishDestinationInterface
{
    /**
     * @return TenantInterface
     */
    public function getTenant();

    /**
     * @param TenantInterface $tenant
     */
    public function setTenant(TenantInterface $tenant);

    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route);

    /**
     * @return RouteInterface
     */
    public function getRoute();
}
