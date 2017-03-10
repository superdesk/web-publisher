<?php

/*
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

use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Interface FileInterface.
 */
interface FileInterface extends PersistableInterface
{
    /**
     * Get uploaded file extension.
     *
     * @return string
     */
    public function getFileExtension();

    /**
     * Get unique asset(file/image) id - usually external one.
     *
     * @return string
     */
    public function getAssetId();
}
