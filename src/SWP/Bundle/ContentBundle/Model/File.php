<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

class File implements FileInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * Uploaded file extension.
     *
     * @var string
     */
    protected $fileExtension;

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileExtension($extension)
    {
        $this->fileExtension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }
}
