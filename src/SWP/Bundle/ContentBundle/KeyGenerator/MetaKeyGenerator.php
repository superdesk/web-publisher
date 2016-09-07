<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\KeyGenerator;

use Asm89\Twig\CacheExtension\CacheStrategy\KeyGeneratorInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * Key generator for meta objects.
 */
class MetaKeyGenerator implements KeyGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateKey($meta)
    {
        if (is_object($meta) && $meta instanceof Meta) {
            $value = $meta->getValues();
            $keyElements = [];

            if ($value instanceof TimestampableInterface) {
                $date = null !== $value->getUpdatedAt() ? $value->getUpdatedAt() : $value->getCreatedAt();
                $keyElements[] = $date->getTimestamp();
            }

            if ($value instanceof PersistableInterface) {
                $keyElements[] = $value->getId();
            }

            return sha1(implode('', $keyElements));
        }

        return sha1(serialize($meta));
    }
}
