<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Bundle\CoreBundle\Entity;

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

class Rule extends \SWP\Component\Rule\Model\Rule implements TenantAwareInterface
{
    protected $tenantCode;

    /**
     * Gets the current tenant (code).
     *
     * @return string Tenant code
     */
    public function getTenantCode()
    {
        return $this->tenantCode;
    }

    /**
     * Sets the tenant (code).
     *
     * @param string $code Tenant code
     */
    public function setTenantCode($code)
    {
        $this->tenantCode = $code;
    }
}
