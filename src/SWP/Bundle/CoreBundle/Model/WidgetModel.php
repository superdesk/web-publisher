<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\TemplatesSystemBundle\Model\WidgetModel as BaseWidgetModel;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

class WidgetModel extends BaseWidgetModel implements TenantAwareInterface, WidgetModelInterface
{
    use TenantAwareTrait;
}
