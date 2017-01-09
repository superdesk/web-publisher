<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\TemplatesSystemBundle\Model\WidgetModel as BaseWidgetModel;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\Revision\RevisionAwareTrait;

class WidgetModel extends BaseWidgetModel implements TenantAwareInterface, RevisionAwareInterface
{
    use TenantAwareTrait, RevisionAwareTrait;
}
