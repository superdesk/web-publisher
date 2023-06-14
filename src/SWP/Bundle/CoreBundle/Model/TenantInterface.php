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
use SWP\Component\Common\Model\ThemeAwareTenantInterface;

interface TenantInterface extends ThemeAwareTenantInterface
{
    /**
     * Gets the homepage.
     *
     * @return RouteInterface
     */
    public function getHomepage();

    /**
     * Sets the homepage.
     *
     * @param RouteInterface $homepage
     */
    public function setHomepage(RouteInterface $homepage);

    /**
     * @param $domainName
     */
    public function setDomainName($domainName);

    /**
     * @return string
     */
    public function getDomainName();

    /**
     * @return bool
     */
    public function isAmpEnabled(): bool;

    /**
     * @param bool $ampEnabled
     */
    public function setAmpEnabled(bool $ampEnabled);

    public function getAppleNewsConfig(): ?AppleNewsConfig;

    public function setAppleNewsConfig(?AppleNewsConfig $appleNewsConfig): void;

    public function getPwaConfig(): ?PWAConfig;

    public function setPwaConfig(?PWAConfig $pwaConfig): void;
}
