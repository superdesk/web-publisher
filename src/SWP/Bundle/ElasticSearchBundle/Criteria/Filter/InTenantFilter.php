<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Bundle\ElasticSearchBundle\Criteria\Filter;

final class InTenantFilter
{
    /**
     * @var string
     */
    private $tenantCode;

    /**
     * @param string $tenantCode
     */
    public function __construct(string $tenantCode)
    {
        $this->tenantCode = $tenantCode;
    }

    /**
     * @return string
     */
    public function getTenantCode()
    {
        return $this->tenantCode;
    }
}
