<?php

namespace SWP\Bundle\WebRendererBundle\Model;

use SWP\Component\Common\Model\TenantInterface;
use SWP\Component\MultiTenancy\Model\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantInterface
{
    /**
     * @var string
     */
    protected $themeName;

    /**
     * {@inheritdoc}
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * {@inheritdoc}
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;
    }
}
