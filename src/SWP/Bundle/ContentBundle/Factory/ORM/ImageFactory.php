<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ImageFactory implements ImageFactoryInterface
{
    protected $imageRepository;

    protected $factory;

    public function __construct(ImageRepositoryInterface $imageRepository, FactoryInterface $factory)
    {
        $this->imageRepository = $imageRepository;
        $this->factory = $factory;
    }

    public function createFromRendition(RenditionInterface $rendition): ?ImageInterface
    {
        $image = $this->findImage($rendition->getMedia());

        if (null === $image) {
            return null;
        }

        return $image;
    }

    protected function findImage(string $mediaId)
    {
        return $this->imageRepository->findImageByAssetId(ArticleMedia::handleMediaId($mediaId));
    }

    public function create(): ImageInterface
    {
        return $this->factory->create();
    }
}
