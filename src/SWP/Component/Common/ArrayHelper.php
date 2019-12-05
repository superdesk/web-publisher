<?php

declare(strict_types=1);

namespace SWP\Component\Common;

class ArrayHelper
{
    public static function sortNestedArrayAssocAlphabeticallyByKey(array $a): array
    {
        ksort($a);
        foreach ($a as $key => $value) {
            if (is_array($value)) {
                $a[$key] = self::sortNestedArrayAssocAlphabeticallyByKey($value);
            }
        }

        return $a;
    }
}
