<?php

declare(strict_types=1);

namespace SWP\Behat\Stamp;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class RedeliveryStamp implements StampInterface
{
    private $retryCount;
    private $redeliveredAt;
    private $exceptionMessage;
    private $flattenException;

    public function __construct(int $retryCount, string $exceptionMessage = null, FlattenException $flattenException = null)
    {
        $this->retryCount = $retryCount;
        $this->exceptionMessage = $exceptionMessage;
        $this->flattenException = $flattenException;
        $this->redeliveredAt = new \DateTimeImmutable('2020-02-18 11:00');
    }

    public static function getRetryCountFromEnvelope(Envelope $envelope): int
    {
        /** @var \Symfony\Component\Messenger\Stamp\RedeliveryStamp|null $retryMessageStamp */
        $retryMessageStamp = $envelope->last(\Symfony\Component\Messenger\Stamp\RedeliveryStamp::class);

        return $retryMessageStamp ? $retryMessageStamp->getRetryCount() : 0;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function getFlattenException(): ?FlattenException
    {
        return $this->flattenException;
    }

    public function getRedeliveredAt(): \DateTimeInterface
    {
        return $this->redeliveredAt;
    }
}
