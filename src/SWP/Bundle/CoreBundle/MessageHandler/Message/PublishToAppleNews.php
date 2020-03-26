<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\MessageHandler\Message;

class PublishToAppleNews implements MessageInterface
{
    /** @var int */
    private $articleId;

    /** @var int */
    private $tenantId;

    public function __construct(int $articleId, int $tenantId)
    {
        $this->articleId = $articleId;
        $this->tenantId = $tenantId;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function toArray(): array
    {
        return [
            'articleId' => $this->articleId,
            'tenantId' => $this->tenantId,
        ];
    }
}
