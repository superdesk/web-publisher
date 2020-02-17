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
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final class FailedEntriesProvider
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
            $redeliveryStamps = $envelope->all(RedeliveryStamp::class);
            foreach ($redeliveryStamps as $redeliveryStamp) {
                $history[] = $redeliveryStamp->getRedeliveredAt();
            }

            $rows[] = new FailedEntry(
                $stamp->getId(),
                get_class($envelope->getMessage()),
                null === $lastRedeliveryStampWithException ? '' : $lastRedeliveryStampWithException->getRedeliveredAt(),
                null === $lastRedeliveryStampWithException ? '' : $lastRedeliveryStampWithException->getExceptionMessage(),
                $sentToFailureTransportStamp->getOriginalReceiverName(),
                $history,
                $envelope->getMessage() instanceof MessageInterface ? $envelope->getMessage()->toArray() : [],
                $flattenException = null === $lastRedeliveryStampWithException ? null : $lastRedeliveryStampWithException->getFlattenException()->getTraceAsString()
            );
        }

        return $rows;
    }

    private function getLastRedeliveryStampWithException(Envelope $envelope): ?RedeliveryStamp
    {
        /** @var RedeliveryStamp $stamp */
        foreach (array_reverse($envelope->all(RedeliveryStamp::class)) as $stamp) {
            if (null !== $stamp->getExceptionMessage()) {
                return $stamp;
            }
        }

        return null;
    }
}
