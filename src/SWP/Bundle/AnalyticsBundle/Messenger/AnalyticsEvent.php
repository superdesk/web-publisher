<?php

declare(strict_types=1);

namespace SWP\Bundle\AnalyticsBundle\Messenger;

class AnalyticsEvent
{
    /** @var string */
    private $httpReferrer;

    /** @var int|null */
    private $articleId;

    /** @var string|null */
    private $pageViewReferrer;

    public function __construct(string $httpReferrer, ?string $articleId, ?string $pageViewReferrer)
    {
        $this->httpReferrer = $httpReferrer;
        $this->articleId = $articleId;
        $this->pageViewReferrer = $pageViewReferrer;
    }

    public function getArticleId(): ?int
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
