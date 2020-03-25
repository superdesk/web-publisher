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
    public function setRoute(RouteInterface $route);

    public function getRoute();

    public function getTenant();

    public function setTenant(TenantInterface $tenant);

    public function isPublishedFbia(): bool;

    public function setIsPublishedFbia(bool $isPublishedFbia): void;

    public function isPublished(): bool;

    public function setPublished(bool $published): void;

    public function getPackageGuid(): ?string;

    public function setPackageGuid(?string $packageGuid): void;

    public function getContentLists(): array;

    public function setContentLists(array $contentLists): void;

    public function isPublishedToAppleNews(): bool;

    public function setIsPublishedToAppleNews(bool $isPublishedToAppleNews): void;
}
