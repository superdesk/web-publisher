<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\RevisionBundle\Model\RevisionLog as BaseRevisionLog;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class RevisionLog extends BaseRevisionLog implements TenantAwareInterface
{
    use TenantAwareTrait;
}
