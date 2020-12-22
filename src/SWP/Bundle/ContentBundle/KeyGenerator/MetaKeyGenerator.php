<?php

declare(strict_types=1);

/*
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

use Twig\CacheExtension\CacheStrategy\KeyGeneratorInterface;
use SWP\Bundle\ContentBundle\Model\ArticlesUpdatedTimeAwareInterface;
use SWP\Bundle\ContentBundle\Model\MediaAwareInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\ContentList\Model\ContentListItemsUpdatedAwareInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class MetaKeyGenerator implements KeyGeneratorInterface
{
    public function generateKey($meta): string
    {
        if (is_object($meta) && $meta instanceof Meta) {
            $value = $meta->getValues();
            $keyElements = [];

            if ($value instanceof TimestampableInterface) {
                $keyElements[] = ($value->getUpdatedAt() ?? $value->getCreatedAt())->getTimestamp();
            }

            if ($value instanceof ArticlesUpdatedTimeAwareInterface && null !== $value->getArticlesUpdatedAt()) {
                $keyElements[] = $value->getArticlesUpdatedAt()->getTimestamp();
            }

            if ($value instanceof ContentListItemsUpdatedAwareInterface && null !== $value->getContentListItemsUpdatedAt()) {
                $keyElements[] = $value->getContentListItemsUpdatedAt()->getTimestamp();
            }

            if ($value instanceof MediaAwareInterface && null !== $value->getMediaUpdatedAt()) {
                $keyElements[] = $value->getMediaUpdatedAt()->getTimestamp();
            }

            if ($value instanceof PersistableInterface) {
                $keyElements[] = $value->getId();
            }

            return sha1(implode('', $keyElements));
        }

        return sha1(serialize($meta));
    }
}
