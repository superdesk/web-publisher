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
    private const THUMBNAIL = 'thumbnail';

    private $thumbnailRenditionName;

    private $viewImageRenditionName;

    public function __construct(string $thumbnailRenditionName, string $viewImageRenditionName)
    {
        $this->thumbnailRenditionName = $thumbnailRenditionName;
        $this->viewImageRenditionName = $viewImageRenditionName;
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
            $thumbnailRendition = $this->copyRendition($data, $this->thumbnailRenditionName, self::THUMBNAIL);
            if ($thumbnailRendition) {
                $data->addRendition($thumbnailRendition);
            }

            $viewImageRendition = $this->copyRendition($data, $this->viewImageRenditionName, self::VIEW_IMAGE);
            if ($viewImageRendition) {
                $data->addRendition($viewImageRendition);
            }
        }
    }

    private function copyRendition(ArticleMediaInterface $media, string $from, string $to): ?ImageRenditionInterface
    {
        // check if rendition we want to create don't exists already in package
        $existingRendition = $media->getRenditions()
            ->filter(static function ($rendition) use ($to) {
                /* @var ImageRenditionInterface $rendition */
                return $to === $rendition->getName();
            });

        if (0 !== count($existingRendition)) {
            return null;
        }

        /** @var ArrayCollection<ImageRenditionInterface> $searchedRenditions */
        $searchedRenditions = $media->getRenditions()
            ->filter(static function ($rendition) use ($from) {
                /* @var ImageRenditionInterface $rendition */
                return $rendition->getName() === $from;
            });

        if (0 === count($searchedRenditions)) {
            return null;
        }

        $copiedRendition = clone $searchedRenditions->first();
        $copiedRendition->setName($to);

        return $copiedRendition;
    }
}
