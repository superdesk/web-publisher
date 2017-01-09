<?php

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\RevisionBundle\Model\Revision as BaseRevision;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class Revision extends BaseRevision implements TenantAwareInterface
{
    use TenantAwareTrait;
}
