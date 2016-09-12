<?php

namespace SWP\Bundle\CoreBundle\Entity;

use Burgov\Bundle\KeyValueFormBundle\KeyValueContainer;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Rule\Model\Rule as BaseRule;

class Rule extends BaseRule implements TenantAwareInterface, PersistableInterface
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

    /*public function setConfiguration($configuration)
    {
        $this->configuration = $this->convertToArray($configuration);
    }

    private function convertToArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof KeyValueContainer) {
            return $data->toArray();
        }

        if ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new \InvalidArgumentException(
            sprintf('Expected array, Traversable or KeyValueContainer, got "%s"',
                is_object($data) ? get_class($data) : gettype($data)));
    }*/
}
