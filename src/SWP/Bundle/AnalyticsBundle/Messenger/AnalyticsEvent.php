<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Messenger;

class AnalyticsEvent
{
    /** @var string */
    private $httpReferrer;

    /** @var int */
    private $articleId;

    /** @var string|null */
    private $pageViewReferrer;

    public function __construct(string $httpReferrer, int $articleId, ?string $pageViewReferrer)
    {
        $this->httpReferrer = $httpReferrer;
        $this->articleId = $articleId;
        $this->pageViewReferrer = $pageViewReferrer;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getHttpReferrer(): string
    {
        return $this->httpReferrer;
    }

    public function getPageViewReferrer(): ?string
    {
        return $this->pageViewReferrer;
    }
}
