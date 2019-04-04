<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractContext
{
    protected function fillObject($object, array $data)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($data as $column => $value) {
            $propertyAccessor->setValue($object, $column, $value);
        }
    }
}
