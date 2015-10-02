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

/**
 * Locale interface for documents.
 */
interface LocaleInterface
{
    /**
     * Initialize Locale object.
     *
     * @param string|null $locale Locale to set, should use setLocale()
     */
    public function __construct($locale = null);

    /**
     * Returns locale according requirements of storage system.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set locale. If invalid locale will be
     *
     * @param string $locale
     *
     * @throws \SWP\ContentBundle\Document\Locale\InvalidLocaleException
     */
    public function setLocale($locale);
}
