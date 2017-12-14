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

use SWP\Component\Webhook\Model\Webhook as BaseWebhook;

class Webhook extends BaseWebhook implements WebhookInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $events;

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
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * {@inheritdoc}
     */
    public function setEvents(string $events): void
    {
        $this->events = $events;
    }
}
