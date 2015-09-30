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

/**
 * Base document for all documents to extend
 */
class BaseDocument implements DocumentInterface
{
    use DocumentTrait;

    /**
     * {@inheritdoc}
     */
    public function isTranslatable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVersionble()
    {
        return false;
    }
}
