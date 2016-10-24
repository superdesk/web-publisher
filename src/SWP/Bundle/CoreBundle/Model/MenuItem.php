<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RouteAwareInterface;
use SWP\Bundle\ContentBundle\Model\RouteAwareTrait;
use SWP\Bundle\MenuBundle\Model\MenuItem as BaseMenuItem;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class MenuItem extends BaseMenuItem implements TenantAwareInterface, RouteAwareInterface
{
    use TenantAwareTrait, RouteAwareTrait;
}
