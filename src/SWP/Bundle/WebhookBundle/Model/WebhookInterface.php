<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Webhook Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebhookBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Webhook\Model\WebhookInterface as BaseWebhookInterface;

/**
 * Interface WebhookInterface.
 */
interface WebhookInterface extends BaseWebhookInterface, PersistableInterface
{
    /**
     * @return int
     */
    public function getId();

    public function getEvents(): array;

    public function setEvents(array $events): void;
}
