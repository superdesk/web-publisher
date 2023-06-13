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
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class FailedEntriesProvider
{
    /** @var ReceiverInterface */
    private ReceiverInterface $receiver;

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
            /** @var SentToFailureTransportStamp|null $sentToFailureTransportStamp */
            $sentToFailureTransportStamp = $envelope->last(SentToFailureTransportStamp::class);

            /** @var TransportMessageIdStamp $stamp */
            $stamp = $envelope->last(TransportMessageIdStamp::class);
            foreach (array_reverse($envelope->all(RedeliveryStamp::class)) as $redeliveryStamp) {
                $history[] = $redeliveryStamp->getRedeliveredAt();
            }

            /**
             * @var ErrorDetailsStamp $errorDetailsStamp
             */
            $errorDetailsStamp = $envelope->last(ErrorDetailsStamp::class);

            $rows[] = new FailedEntry(
                (int) $stamp->getId(),
                get_class($envelope->getMessage()),
                $history[0],
                $errorDetailsStamp->getExceptionMessage(),
                $sentToFailureTransportStamp->getOriginalReceiverName(),
                $history,
                $envelope->getMessage() instanceof MessageInterface ? $envelope->getMessage()->toArray() : [],
                $errorDetailsStamp->getFlattenException()->getTraceAsString()
            );
        }

        return $rows;
    }
}
