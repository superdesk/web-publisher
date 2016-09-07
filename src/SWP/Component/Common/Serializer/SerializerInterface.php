<?php

/**
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Serializer;

interface SerializerInterface
{
    /**
     * Serializes the given data to the specified output format.
     *
     * @param mixed  $data
     * @param string $format
     *
     * @return string
     */
    public function serialize($data, $format);

    /**
     * De-serializes the given data to the specified type.
     *
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return mixed
     */
    public function deserialize($data, $type, $format);
}
