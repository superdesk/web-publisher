<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Api\Response;

final class AppleNewsArticle
{
    private $articleId;

    private $shareUrl;

    private $revisionId;

    public function __construct(
        string $articleId,
        string $shareUrl,
        string $revisionId)
    {
        $this->articleId = $articleId;
        $this->shareUrl = $shareUrl;
        $this->revisionId = $revisionId;
    }

    public function getArticleId(): string
    {
        return $this->articleId;
    }

    public function getShareUrl(): string
    {
        return $this->shareUrl;
    }

    public function getRevisionId(): string
    {
        return $this->revisionId;
    }

    public static function fromRawResponse(array $response): self
    {
        return new self(
            $response['data']['id'],
            $response['data']['shareUrl'],
            $response['revision']
        );
    }
}
