<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Document\Locale;

class DefaultLocale implements LocaleInterface
{
    /**
     * Locale string
     *
     * @var string
     */
    protected $locale;

    /**
     * {@inheritdoc}
     */
    public function __construct($locale = null)
    {
        if (!is_null($locale)) {
            $this->setLocale($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        $localeParts = Locale::parse_locale($this->locale);
        return sprintf(
            '%s-%s',
            $localeParts[Locale::LANG_TAG],
            $localeParts[Locale::REGION_TAG]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        // Always expect language, region and script
        $localeParts = Locale::parse_locale($locale);

        if (
            !is_array($localeParts) ||
            empty($localeParts) ||
            !isset($localeParts[Locale::LANG_TAG]) ||
            !isset($localeParts[Locale::REGION_TAG]) ||
            !isset($localeParts[Locale::SCRIPT_TAG])
        ) {
            throw new InvalidLocaleException('Invalid locale. Locale should at least consists of language, region and script information.');
        }

        $this->locale = $locale;
    }
}
