<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\CoreBundle\Model\ArticleMedia;
use SWP\Bundle\CoreBundle\Model\ArticleMediaInterface;

final class ArticleMediaSerializationSubscriber implements EventSubscriberInterface
{
    private const VIEW_IMAGE = 'viewImage';

    private $thumbnailRenditionName;

    public function __construct(string $thumbnailRenditionName)
    {
        $this->thumbnailRenditionName = $thumbnailRenditionName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => ArticleMedia::class,
                'method' => 'onPreSerialize',
            ],
        ];
    }

    public function onPreSerialize(PreSerializeEvent $event): void
    {
        /** @var ArticleMediaInterface $data */
        $data = $event->getObject();
        /** @var ImageRenditionInterface[] $renditions */
        if (null !== ($renditions = $data->getRenditions()) && count($renditions) > 0) {
            $existingThumbnailRendition = $data->getRenditions()
                ->filter(static function ($rendition) {
                    /* @var ImageRenditionInterface $rendition */
                    return self::VIEW_IMAGE === $rendition->getName();
                });
            if (0 !== count($existingThumbnailRendition)) {
                return;
            }

            /** @var ArrayCollection<ImageRenditionInterface> $searchedRenditions */
            $searchedRenditions = $data->getRenditions()
                ->filter(function ($rendition) {
                    /* @var ImageRenditionInterface $rendition */
                    return $rendition->getName() === $this->thumbnailRenditionName;
                });

            if (0 === count($searchedRenditions)) {
                return;
            }
            $thumbnailRendition = clone $searchedRenditions->first();
            $thumbnailRendition->setName(self::VIEW_IMAGE);
            $data->addRendition($thumbnailRendition);
        }
    }
}
