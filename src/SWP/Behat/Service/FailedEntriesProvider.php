<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Behat\Stamp\RedeliveryStamp;
use SWP\Bundle\CoreBundle\Provider\FailedEntriesProvider as BaseFailedEntriesProvider;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;

class FailedEntriesProvider extends BaseFailedEntriesProvider
{
    protected function getRedeliveryStamps(Envelope $envelope): array
    {
        return $envelope->all(RedeliveryStamp::class);
    }

    protected function getExceptionTraceAsString(StampInterface $lastRedeliveryStampWithException): ?string
    {
        return 'stack trace exception';
    }
}
