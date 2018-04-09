<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\MultiTenancy\Model\OrganizationInterface as BaseOrganizationInterface;

interface OrganizationInterface extends BaseOrganizationInterface
{
    /**
     * @return Collection
     */
    public function getPublishDestinations(): Collection;

    /**
     * @param Collection $publishDestinations
     */
    public function setPublishDestinations(Collection $publishDestinations): void;

    /**
     * @param PublishDestinationInterface $publishDestination
     */
    public function addPublishDestination(PublishDestinationInterface $publishDestination): void;

    /**
     * @param PublishDestinationInterface $publishDestination
     */
    public function removePublishDestination(PublishDestinationInterface $publishDestination): void;

    /**
     * @param PublishDestinationInterface $publishDestination
     *
     * @return bool
     */
    public function hasPublishDestination(PublishDestinationInterface $publishDestination): bool;

    /**
     * @return null|string
     */
    public function getSecretToken(): ?string;

    /**
     * @param null|string $secretToken
     */
    public function setSecretToken(?string $secretToken): void;
}
