<?php

declare(strict_types=1);
/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
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

/**
 * Interface ArticleEventsInterface.
 */
interface ArticleEventsInterface
{
    const ACTION_IMPRESSION = 'impression';

    const ACTION_PAGEVIEW = 'pageview';

    const ACTION_LINK_CLICKED = 'linkclicked';

    const ACTION_SCROLL_DEPTH = 'scrolldepth';

    /**
     * @return string
     */
    public function getAction(): string;

    /**
     * @param string $action
     */
    public function setAction(string $action): void;

    /**
     * @return null|string
     */
    public function getValue(): ?string;

    /**
     * @param null|string $value
     */
    public function setValue(?string $value): void;
}
