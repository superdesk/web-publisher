<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\TemplatesSystemBundle\Model\WidgetModel as BaseWidgetModel;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class WidgetModel extends BaseWidgetModel implements TenantAwareInterface
{
    use TenantAwareTrait;
}
