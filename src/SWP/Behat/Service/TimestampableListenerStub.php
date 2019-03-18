<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Bundle\ContentBundle\EventListener\TimestampableListener;
use SWP\Component\Common\Model\DateTime;

class TimestampableListenerStub extends TimestampableListener
{
    protected function getFieldValue($meta, $field, $eventAdapter): \DateTimeInterface
    {
        return DateTime::getCurrentDateTime();
    }
}
