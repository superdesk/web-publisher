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
use SWP\Component\OutputChannel\Model\OutputChannelAwareInterface;
use SWP\Component\OutputChannel\Model\OutputChannelInterface as BaseOutputChannelInterface;

class Tenant extends BaseTenant implements TenantInterface, ArticlesCountInterface, OutputChannelAwareInterface
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
     * @var BaseOutputChannelInterface|null
     */
    protected $outputChannel;

    /** @var AppleNewsConfig|null */
    protected $appleNewsConfig;

    protected ?PWAConfig $pwaConfig = null;

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

    /**
     * {@inheritdoc}
     */
    public function getOutputChannel(): ?BaseOutputChannelInterface
    {
        return $this->outputChannel;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutputChannel(?BaseOutputChannelInterface $outputChannel): void
    {
        $this->outputChannel = $outputChannel;

        if ($outputChannel instanceof OutputChannelInterface) {
            $outputChannel->setTenant($this);
        }
    }

    public function getAppleNewsConfig(): ?AppleNewsConfig
    {
        return $this->appleNewsConfig;
    }

    public function setAppleNewsConfig(?AppleNewsConfig $appleNewsConfig): void
    {
        if ($appleNewsConfig instanceof AppleNewsConfig) {
            $appleNewsConfig->setTenant($this);
        }

        $this->appleNewsConfig = $appleNewsConfig;
    }

    /**
     * @return PWAConfig
     */
    public function getPwaConfig(): ?PWAConfig
    {
        return $this->pwaConfig;
    }

    /**
     * @param PWAConfig $pwaConfig
     */
    public function setPwaConfig(?PWAConfig $pwaConfig): void
    {
        $this->pwaConfig = $pwaConfig;
    }
}
