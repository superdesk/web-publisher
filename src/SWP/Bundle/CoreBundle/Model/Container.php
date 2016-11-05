<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\TemplatesSystemBundle\Model\Container as BaseContainer;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

class Container extends BaseContainer implements TenantAwareInterface, ContainerInterface
{
    use TenantAwareTrait;
}
