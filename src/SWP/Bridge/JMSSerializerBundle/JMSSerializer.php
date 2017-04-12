<?php

/*
 * This file is part of the Superdesk Web Publisher JMSSerializerBundle Bridge.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bridge\JMSSerializerBundle;

use JMS\Serializer\SerializerInterface as BaseSerializerInterface;
use SWP\Component\Common\Serializer\SerializerInterface;

class JMSSerializer implements SerializerInterface
{
    /**
     * @var BaseSerializerInterface
     */
    protected $serializer;

    /**
     * JMSSerializer constructor.
     *
     * @param BaseSerializerInterface $serializer
     */
    public function __construct(BaseSerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format)
    {
        return $this->serializer->serialize($data, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format)
    {
        return $this->serializer->deserialize($data, $type, $format);
    }
}
