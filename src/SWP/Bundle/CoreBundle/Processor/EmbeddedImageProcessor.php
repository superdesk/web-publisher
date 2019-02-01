<?php

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

namespace SWP\Bundle\CoreBundle\Processor;

use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Processor\EmbeddedImageProcessor as BaseEmbeddedImageProcessor;
use SWP\Bundle\CoreBundle\Context\ArticlePreviewContextInterface;

final class EmbeddedImageProcessor extends BaseEmbeddedImageProcessor
{
    /**
     * @var ArticlePreviewContextInterface
     */
    private $articlePreviewContext;

    public function __construct(
        MediaManagerInterface $mediaManager,
        FileExtensionCheckerInterface $fileExtensionChecker,
        ArticlePreviewContextInterface $articlePreviewContext
    ) {
        parent::__construct($mediaManager, $fileExtensionChecker);
        $this->articlePreviewContext = $articlePreviewContext;
    }

    protected function processImageElement(\DOMElement $imageElement, ImageRendition $rendition, string $mediaId)
    {
        parent::processImageElement($imageElement, $rendition, $mediaId);

        if ($this->articlePreviewContext->isPreview()) {
            $imageElement->setAttribute('src', $rendition->getPreviewUrl());
        }
    }
}
