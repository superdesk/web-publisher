<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Paywall\Model\PaywallSecuredInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface PublishDestinationInterface extends TimestampableInterface, PersistableInterface, PaywallSecuredInterface
{
    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route);

    /**
     * @return RouteInterface|null
     */
    public function getRoute();

    /**
     * @return TenantInterface|null
     */
    public function getTenant();

    /**
     * @param TenantInterface $tenant
     */
    public function setTenant(TenantInterface $tenant);

    /**
     * @return bool
     */
    public function isPublishedFbia(): bool;

    /**
     * @param bool $isPublishedFbia
     */
    public function setIsPublishedFbia(bool $isPublishedFbia): void;

    /**
     * @return bool
     */
    public function isPublished(): bool;

    /**
     * @param bool $published
     */
    public function setPublished(bool $published): void;

    /**
     * @return null|string
     */
    public function getPackageGuid(): ?string;

    /**
     * @param null|string $packageGuid
     */
    public function setPackageGuid(?string $packageGuid): void;
}
