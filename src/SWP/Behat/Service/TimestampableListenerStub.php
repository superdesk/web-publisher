<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Bundle\ContentBundle\EventListener\TimestampableListener;

class TimestampableListenerStub extends TimestampableListener
{
    private $dateTimeService;

    public function __construct(DateTimeService $dateTimeService)
    {
        parent::__construct();

        $this->dateTimeService = $dateTimeService;
    }

    protected function getFieldValue($meta, $field, $eventAdapter): \DateTimeInterface
    {
        if (null === $this->dateTimeService->getCurrentDateTime()) {
            return parent::getFieldValue($meta, $field, $eventAdapter);
        }

        return $this->dateTimeService->getCurrentDateTime();
    }
}
