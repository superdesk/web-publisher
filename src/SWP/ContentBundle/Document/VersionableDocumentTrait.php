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

trait VersionableDocumentTrait
{
    // TODO: Check if we should implement a PHPCR specific implementation for this
    // or if that should into a seperate trait
    protected $versionName;

    protected $versionCreated;

    public function getVersionName()
    {
        return $this->versionName;
    }

    public function getVersionCreated()
    {
        return $this->versionCreated;
    }

    /**
     * {@inheritdoc}
     */
    public function isVersionable()
    {
        return true;
    }
}
