<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSeoMediaInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSeoMetadataInterface;
use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class SeoImageUploader implements SeoImageUploaderInterface
{
    private $randomStringGenerator;

    private $mediaManager;

    private $factory;

    public function __construct(GeneratorInterface $randomStringGenerator, MediaManagerInterface $mediaManager, FactoryInterface $factory)
    {
        $this->randomStringGenerator = $randomStringGenerator;
        $this->mediaManager = $mediaManager;
        $this->factory = $factory;
    }

    public function handleUpload(ArticleSeoMetadataInterface $seoMetadata): void
    {
        if (null !== ($file = $seoMetadata->getMetaMediaFile())) {
            $image = $this->mediaManager->handleUploadedFile($file, $this->randomStringGenerator->generate(15));
            $seoImageMedia = $this->factory->create();
            $seoImageMedia->setKey(ArticleSeoMediaInterface::MEDIA_META_KEY);
            $seoImageMedia->setImage($image);

            $seoMetadata->setMetaMedia($seoImageMedia);
        }

        if (null !== ($file = $seoMetadata->getOgMediaFile())) {
            $image = $this->mediaManager->handleUploadedFile($file, $this->randomStringGenerator->generate(15));
            $seoImageMedia = $this->factory->create();
            $seoImageMedia->setKey(ArticleSeoMediaInterface::MEDIA_OG_KEY);
            $seoImageMedia->setImage($image);

            $seoMetadata->setOgMedia($seoImageMedia);
        }

        if (null !== ($file = $seoMetadata->getTwitterMediaFile())) {
            $image = $this->mediaManager->handleUploadedFile($file, $this->randomStringGenerator->generate(15));
            $seoImageMedia = $this->factory->create();
            $seoImageMedia->setKey(ArticleSeoMediaInterface::MEDIA_TWITTER_KEY);
            $seoImageMedia->setImage($image);

            $seoMetadata->setTwitterMedia($seoImageMedia);
        }
    }
}
