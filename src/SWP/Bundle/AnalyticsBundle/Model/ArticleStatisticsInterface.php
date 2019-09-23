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

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ArticleStatisticsInterface extends PersistableInterface
{
    public function getArticle(): ArticleInterface;

    public function setArticle(ArticleInterface $article): void;

    public function getImpressionsNumber(): int;

    public function setImpressionsNumber(int $impressionsNumber): void;

    public function getPageViewsNumber(): int;

    public function setPageViewsNumber(int $pageViewsNumber): void;

    public function getInternalClickRate(): float;

    public function setInternalClickRate(float $internalClickRate): void;
}
