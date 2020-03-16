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

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Bundle\CoreBundle\MessageHandler\Message\MessageInterface;
use SWP\Bundle\CoreBundle\Model\FailedEntry;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class FailedEntriesProvider
{
    /** @var ReceiverInterface */
    private $receiver;

    public function __construct(ReceiverInterface $receiver)
    {
        $this->receiver = $receiver;
    }

    public function getFailedEntries(?int $max): array
    {
        $envelopes = $this->receiver->all($max);

        $rows = [];
        $history = [];
        foreach ($envelopes as $envelope) {
            $lastRedeliveryStampWithException = $this->getLastRedeliveryStampWithException($envelope);
            /** @var SentToFailureTransportStamp|null $sentToFailureTransportStamp */
            $sentToFailureTransportStamp = $envelope->last(SentToFailureTransportStamp::class);

            /** @var TransportMessageIdStamp $stamp */
            $stamp = $envelope->last(TransportMessageIdStamp::class);
            $redeliveryStamps = $this->getRedeliveryStamps($envelope);
            foreach ($redeliveryStamps as $redeliveryStamp) {
                $history[] = $redeliveryStamp->getRedeliveredAt();
            }

            $rows[] = new FailedEntry(
                (int) $stamp->getId(),
                get_class($envelope->getMessage()),
                null === $lastRedeliveryStampWithException ? null : $lastRedeliveryStampWithException->getRedeliveredAt(),
                null === $lastRedeliveryStampWithException ? null : $lastRedeliveryStampWithException->getExceptionMessage(),
                $sentToFailureTransportStamp->getOriginalReceiverName(),
                $history,
                $envelope->getMessage() instanceof MessageInterface ? $envelope->getMessage()->toArray() : [],
                $this->getExceptionTraceAsString($lastRedeliveryStampWithException)
            );
        }

        return $rows;
    }

    private function getLastRedeliveryStampWithException(Envelope $envelope): ?StampInterface
    {
        /** @var RedeliveryStamp $stamp */
        foreach (array_reverse($this->getRedeliveryStamps($envelope)) as $stamp) {
            if (null !== $stamp->getExceptionMessage()) {
                return $stamp;
            }
        }

        return null;
    }

    protected function getRedeliveryStamps(Envelope $envelope): array
    {
        return $envelope->all(RedeliveryStamp::class);
    }

    protected function getExceptionTraceAsString(StampInterface $lastRedeliveryStampWithException): ?string
    {
        /* @var RedeliveryStamp $lastRedeliveryStampWithException */
        return null === $lastRedeliveryStampWithException ? null : $lastRedeliveryStampWithException->getFlattenException()->getTraceAsString();
    }
}
