<?php

declare(strict_types=1);

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

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Bundle\ContentBundle\Model\Route as BaseRoute;
use SWP\Component\Paywall\Model\PaywallSecuredInterface;

/**
 * Class Route.
 */
class Route extends BaseRoute implements TenantAwareInterface, ArticlesCountInterface, PaywallSecuredInterface
{
    use TenantAwareTrait, ArticlesCountTrait;

    /**
     * @var bool
     */
    protected $paywallSecured = false;

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $parentSerializedData = unserialize(parent::serialize());
        $parentSerializedData['id'] = $this->getId();

        return serialize($parentSerializedData);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        parent::unserialize($serialized);

        $data = unserialize($serialized);
        $this->id = $data['id'];
    }

    public function isPaywallSecured(): bool
    {
        return $this->paywallSecured;
    }

    public function setPaywallSecured(bool $paywallSecured): void
    {
        $this->paywallSecured = $paywallSecured;
    }
}
