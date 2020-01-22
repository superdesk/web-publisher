<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use DateTime;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractContext
{
    protected function fillObject($object, array $data)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $column => $value) {
            if (is_string($value) && $this->isDate($value)) {
                $value = new DateTime($value);
            }

            $propertyAccessor->setValue($object, $column, $value);
        }
    }

    protected function isDate(string $date): bool
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }
}
