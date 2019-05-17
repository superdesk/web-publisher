<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Factory;

use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Factory\ORM\ImageRenditionFactoryInterface;
use SWP\Bundle\ContentBundle\File\FileDownloaderInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Provider\ORM\ArticleMediaAssetProviderInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Bundle\ContentBundle\Factory\ORM\MediaFactory as BaseMediaFactory;

final class MediaFactory extends BaseMediaFactory
{
    public function __construct(
        ArticleMediaAssetProviderInterface $articleMediaAssetProvider,
        FactoryInterface $factory,
        ImageRenditionFactoryInterface $imageRenditionFactory,
        MediaManagerInterface $mediaManager,
        LoggerInterface $logger,
        FileDownloaderInterface $fileDownloader
    ) {
        parent::__construct($articleMediaAssetProvider, $factory, $imageRenditionFactory, $mediaManager, $logger, $fileDownloader);
    }
}
