<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides the filters themes relied on from the abandoned twig/extensions
 * package (Text and Intl extensions), so existing themes keep working on Twig 3.
 */
class LegacyFiltersExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', [$this, 'truncate'], ['needs_environment' => true]),
            new TwigFilter('wordwrap', [$this, 'wordwrap'], ['needs_environment' => true]),
            new TwigFilter('localizeddate', [$this, 'localizedDate'], ['needs_environment' => true]),
            new TwigFilter('localizednumber', [$this, 'localizedNumber']),
            new TwigFilter('localizedcurrency', [$this, 'localizedCurrency']),
        ];
    }

    public function truncate(Environment $env, ?string $value, int $length = 30, bool $preserve = false, string $separator = '...'): string
    {
        $value = (string) $value;

        if (mb_strlen($value, $env->getCharset()) <= $length) {
            return $value;
        }

        if ($preserve) {
            // If breakpoint is on the last word, return the value without separator.
            if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                return $value;
            }

            $length = $breakpoint;
        }

        return rtrim(mb_substr($value, 0, $length, $env->getCharset())).$separator;
    }

    public function wordwrap(Environment $env, ?string $value, int $length = 80, string $separator = "\n", bool $preserve = false): string
    {
        $value = (string) $value;
        $sentences = [];

        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());

        $pieces = mb_split($separator, $value);
        mb_regex_encoding($previous);

        foreach ($pieces as $piece) {
            while (!$preserve && mb_strlen($piece, $env->getCharset()) > $length) {
                $sentences[] = mb_substr($piece, 0, $length, $env->getCharset());
                $piece = mb_substr($piece, $length, 2048, $env->getCharset());
            }

            $sentences[] = $piece;
        }

        return implode($separator, $sentences);
    }

    /**
     * @param \DateTimeInterface|string|int|null $date
     */
    public function localizedDate(Environment $env, $date, string $dateFormat = 'medium', string $timeFormat = 'medium', ?string $locale = null, $timezone = null, ?string $format = null, string $calendar = 'gregorian')
    {
        $formats = [
            'none' => \IntlDateFormatter::NONE,
            'short' => \IntlDateFormatter::SHORT,
            'medium' => \IntlDateFormatter::MEDIUM,
            'long' => \IntlDateFormatter::LONG,
            'full' => \IntlDateFormatter::FULL,
        ];

        $date = $env->getExtension(\Twig\Extension\CoreExtension::class)->convertDate($date, $timezone);

        $formatter = \IntlDateFormatter::create(
            $locale,
            $formats[$dateFormat] ?? \IntlDateFormatter::MEDIUM,
            $formats[$timeFormat] ?? \IntlDateFormatter::MEDIUM,
            \IntlTimeZone::createTimeZone($date->getTimezone()->getName()),
            'gregorian' === $calendar ? \IntlDateFormatter::GREGORIAN : \IntlDateFormatter::TRADITIONAL,
            $format
        );

        return $formatter->format($date->getTimestamp());
    }

    /**
     * @param int|float|string|null $number
     */
    public function localizedNumber($number, string $style = 'decimal', string $type = 'default', ?string $locale = null): string
    {
        static $typeValues = [
            'default' => \NumberFormatter::TYPE_DEFAULT,
            'int32' => \NumberFormatter::TYPE_INT32,
            'int64' => \NumberFormatter::TYPE_INT64,
            'double' => \NumberFormatter::TYPE_DOUBLE,
        ];

        $formatter = self::getNumberFormatter($locale, $style);

        return $formatter->format($number, $typeValues[$type] ?? \NumberFormatter::TYPE_DEFAULT);
    }

    /**
     * @param int|float|string|null $number
     */
    public function localizedCurrency($number, ?string $currency = null, ?string $locale = null): string
    {
        $formatter = self::getNumberFormatter($locale, 'currency');

        return $formatter->formatCurrency((float) $number, $currency ?? $formatter->getTextAttribute(\NumberFormatter::CURRENCY_CODE));
    }

    private static function getNumberFormatter(?string $locale, string $style): \NumberFormatter
    {
        static $styleValues = [
            'decimal' => \NumberFormatter::DECIMAL,
            'currency' => \NumberFormatter::CURRENCY,
            'percent' => \NumberFormatter::PERCENT,
            'scientific' => \NumberFormatter::SCIENTIFIC,
            'spellout' => \NumberFormatter::SPELLOUT,
            'ordinal' => \NumberFormatter::ORDINAL,
            'duration' => \NumberFormatter::DURATION,
        ];

        return \NumberFormatter::create(
            $locale ?? \Locale::getDefault(),
            $styleValues[$style] ?? \NumberFormatter::DECIMAL
        );
    }
}
