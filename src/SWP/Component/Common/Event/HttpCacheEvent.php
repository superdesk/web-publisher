<?php

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated HttpCacheEvent is deprecated from 2.0.1, will be removed in 3.0
 */
class HttpCacheEvent extends Event
{
    public const EVENT_NAME = 'swp_http_cache.clear';

    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}
