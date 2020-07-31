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
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\MultiTenancy\Model\OrganizationAwareTrait;
use SWP\Component\Paywall\Model\PaywallSecuredTrait;

class PublishDestination implements PublishDestinationInterface
{
    use TimestampableTrait;
    use OrganizationAwareTrait;
    use PaywallSecuredTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var TenantInterface
     */
    protected $tenant;

    /**
     * @var RouteInterface
     */
    protected $route;

    /**
     * @var bool
     */
    protected $isPublishedFbia = true;

    /**
     * @var bool
     */
    protected $published = true;

    /**
     * @var string
     */
    protected $packageGuid;

    /**
     * @var array
     */
    protected $contentLists = [];

    /**
     * @var bool
     */
    protected $isPublishedToAppleNews = false;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishedFbia(): bool
    {
        return $this->isPublishedFbia;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPublishedFbia(bool $isPublishedFbia): void
    {
        $this->isPublishedFbia = $isPublishedFbia;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageGuid(): ?string
    {
        return $this->packageGuid;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackageGuid(?string $packageGuid): void
    {
        $this->packageGuid = $packageGuid;
    }

    public function getContentLists(): array
    {
        if (null === $this->contentLists) {
            return [];
        }

        return $this->contentLists;
    }

    public function setContentLists(array $contentLists): void
    {
        $this->contentLists = $contentLists;
    }

    public function isPublishedToAppleNews(): bool
    {
        return $this->isPublishedToAppleNews;
    }

    public function setIsPublishedToAppleNews(bool $isPublishedToAppleNews): void
    {
        $this->isPublishedToAppleNews = $isPublishedToAppleNews;
    }
}
