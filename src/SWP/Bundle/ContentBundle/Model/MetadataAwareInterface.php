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

interface MetadataAwareInterface
{
    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata);

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getMetadataByKey($key);
}
