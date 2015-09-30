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

trait DocumentTrait
{
    /**
     * Identifier of the object
     *
     * @var mixed
     */
    protected $id;

    /**
     * Creation date
     *
     * @var \DateTime
     */
    protected $created;

    /**
     * Last modification date
     *
     * @var \DateTime
     */
    protected $lastModified;

    /**
     * Gets the document identifier
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }
}
