<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace SWP\Component\MultiTenancy\PathBuilder;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class PathBuilder
{
    /**
     * @var SegmentBagInterface
     */
    protected $segmentBag;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    public function __construct(SegmentBagInterface $segmentBag, TenantContextInterface $tenantContext)
    {
        $this->segmentBag = $segmentBag;
        $this->tenantContext = $tenantContext;
    }

    public function build(array $segments = [], $currentTenantcontext = false)
    {
        $results = [];
        foreach ($segments as $key => $segment) {
            $result = $this->buildSegment($segment);
            if (null === $result) {
                continue;
            }

            $results[] = $result;
            if ($currentTenantcontext && 0 === $key) {
                $tenant = $this->tenantContext->getTenant();
                $results[] = $tenant->getSubdomain();
            }
        }

        return '/'.implode('/', $results);
    }

    private function buildSegment($segment)
    {
        if (empty($segment) || $segment == '/') {
            return;
        }

        if (substr($segment, 0, 1) == '%') {
            if (substr($segment, -1) == '%') {
                return $this->segmentBag->get(substr($segment, 1, -1));
            }
        }

        return $segment;
    }
}
