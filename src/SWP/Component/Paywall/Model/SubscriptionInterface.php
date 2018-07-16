<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Paywall Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Paywall\Model;

use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;

interface SubscriptionInterface extends TimestampableInterface, SoftDeletableInterface
{
    public function getId();

    public function setId($id): void;

    public function getCode(): ?string;

    public function setCode(?string $code): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getDetails(): array;

    public function setDetails(array $details): void;
}
