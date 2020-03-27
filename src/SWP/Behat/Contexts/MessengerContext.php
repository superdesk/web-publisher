<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use SWP\Behat\Stamp\RedeliveryStamp;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMessage;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Transport\Doctrine\DoctrineTransport;

class MessengerContext extends AbstractContext implements Context
{
    private $failureTransport;

    public function __construct(
        DoctrineTransport $failureTransport
    ) {
        $this->failureTransport = $failureTransport;
    }

    /**
     * @Given the failed items exist in the failure queue
     */
    public function theFailedItemsExistInTheFailureQueue()
    {
        $throwable = new \Exception('error');
        $envelope = new Envelope(new ContentPushMessage(1, 'some content'));
        $flattenedException = class_exists(FlattenException::class) ? FlattenException::createFromThrowable($throwable) : null;
        $envelope = $envelope->with(
            new SentToFailureTransportStamp('messenger.transport.failed'),
            new DelayStamp(0),
            new RedeliveryStamp(0, $throwable->getMessage(), $flattenedException)
        );

        $this->failureTransport->send($envelope);
    }
}
