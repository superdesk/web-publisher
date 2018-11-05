<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Factory;

use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePreviewerInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Bundle\ContentBundle\Factory\ORM\ImageFactory as BaseImageFactory;
use SWP\Component\Storage\Factory\FactoryInterface;

class ImageFactory extends BaseImageFactory
{
    protected $articlePreviewer;

    public function __construct(ImageRepositoryInterface $imageRepository, FactoryInterface $factory, ArticlePreviewerInterface $articlePreviewer)
    {
        $this->articlePreviewer = $articlePreviewer;

        parent::__construct($imageRepository, $factory);
    }

    public function createFromRendition(RenditionInterface $rendition): ?ImageInterface
    {
        $image = $this->findImage($rendition->getMedia());

        if (null === $image && $this->articlePreviewer->isPreview()) {
            $image = $this->create();
        }

        return $image;
    }
}
