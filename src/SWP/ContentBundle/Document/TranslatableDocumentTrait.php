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

namespace SWP\ContentBundle\Document;

use SWP\ContentBundle\Document\LocaleInterface;

trait TranslatableDocumentTrait
{
    /**
     * Locale for document
     *
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * Returns locale
     *
     * @return VersionInterface
     */
    public function getLocale()
    {
        return $this->locale();
    }

    /**
     * {@inheritdoc}
     */
    public function isTranslatable()
    {
        return true;
    }
}
