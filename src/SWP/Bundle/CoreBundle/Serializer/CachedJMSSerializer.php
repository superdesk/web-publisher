<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use SWP\Bridge\JMSSerializerBundle\JMSSerializer;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

final class CachedJMSSerializer extends JMSSerializer
{
    private $cachedData = [];

    public function serialize($data, $format)
    {
        $cacheKey = $this->getCacheKey($data, null, $format);
        if (null !== $cacheKey && \array_key_exists($cacheKey, $this->cachedData)) {
            return $this->cachedData[$cacheKey];
        }

        $result = parent::serialize($data, $format);

        if (null !== $cacheKey) {
            $this->cachedData[$cacheKey] = $result;
        }

        return $result;
    }

    public function deserialize($data, $type, $format)
    {
        $cacheKey = $this->getCacheKey($data, $type, $format);
        if (null !== $cacheKey && \array_key_exists($cacheKey, $this->cachedData)) {
            return $this->cachedData[$cacheKey];
        }

        $result = parent::deserialize($data, $type, $format);

        if (null !== $cacheKey) {
            $this->cachedData[$cacheKey] = $result;
        }

        return $result;
    }

    private function getCacheKey($data, string $type = null, string $format = null): ?string
    {
        if ($data instanceof TimestampableInterface && $data instanceof PersistableInterface) {
            return $data->getId().'__'.$data->getCreatedAt()->getTimestamp().'__'.$data->getUpdatedAt()->getTimestamp().'__'.$format;
        }

        if (\is_string($data)) {
            return md5($data.'__'.$type.'__'.$format);
        }

        return null;
    }
}
