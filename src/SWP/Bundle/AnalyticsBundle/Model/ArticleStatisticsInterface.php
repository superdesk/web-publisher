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

use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Interface ArticleStatisticsInterface.
 */
interface ArticleStatisticsInterface extends PersistableInterface
{
    /**
     * @return ArticleInterface
     */
    public function getArticle(): ArticleInterface;

    /**
     * @param ArticleInterface $article
     */
    public function setArticle(ArticleInterface $article): void;

    /**
     * @return int
     */
    public function getImpressionsNumber(): int;

    /**
     * @param int $impressionsNumber
     */
    public function setImpressionsNumber(int $impressionsNumber): void;

    /**
     * @return int
     */
    public function getPageViewsNumber(): int;

    /**
     * @param int $pageViewsNumber
     */
    public function setPageViewsNumber(int $pageViewsNumber): void;

    /**
     * @return Collection
     */
    public function getEvents(): Collection;

    /**
     * @param Collection $events
     */
    public function setEvents(Collection $events): void;

    /**
     * @param ArticleEventInterface $articleEvent
     */
    public function addEvent(ArticleEventInterface $articleEvent);

    public function increasePageViewsNumber(): void;

    public function increaseImpressionsNumber(): void;

    public function getInternalClickRate(): float;

    public function setInternalClickRate(float $internalClickRate): void;
}
