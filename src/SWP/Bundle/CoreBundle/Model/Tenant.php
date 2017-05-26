<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\MultiTenancy\Model\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantInterface, ArticlesCountInterface
{
    use ArticlesCountTrait;

    /**
     * @var string
     */
    protected $themeName;

    /**
     * @var RouteInterface
     */
    protected $homepage;

    /**
     * @var bool
     */
    protected $ampEnabled = false;

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

    /**
     * {@inheritdoc}
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * {@inheritdoc}
     */
    public function setHomepage(RouteInterface $homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * {@inheritdoc}
     */
    public function isAmpEnabled(): bool
    {
        return $this->ampEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmpEnabled(bool $ampEnabled)
    {
        $this->ampEnabled = $ampEnabled;
    }
}
