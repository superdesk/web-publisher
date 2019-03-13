<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Model;

trait ContentListsAwareTrait
{
    protected $contentLists = [];

    public function getContentLists(): array
    {
        return $this->contentLists;
    }

    public function setContentLists(array $contentLists): void
    {
        $this->contentLists = $contentLists;
    }
}
