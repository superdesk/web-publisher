<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Model;

class ContentListAction
{
    public const ACTION_MOVE = 'move';

    public const ACTION_ADD = 'add';

    public const ACTION_DELETE = 'delete';

    /** @var int */
    private $position = 0;

    /** @var bool */
    private $sticky = false;

    /** @var string|null */
    private $action;

    /** @var int */
    private $contentId;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function isSticky(): bool
    {
        return $this->sticky;
    }

    public function setSticky(bool $sticky = false): void
    {
        $this->sticky = $sticky;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getContentId(): ?int
    {
        return $this->contentId;
    }

    public function setContentId(int $contentId): void
    {
        $this->contentId = $contentId;
    }
}
