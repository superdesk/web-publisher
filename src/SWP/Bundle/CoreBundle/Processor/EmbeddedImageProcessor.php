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
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Processor\EmbeddedImageProcessor as BaseEmbeddedImageProcessor;
use SWP\Bundle\CoreBundle\Context\ArticlePreviewContextInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

final class EmbeddedImageProcessor extends BaseEmbeddedImageProcessor
{
    /**
     * @var ArticlePreviewContextInterface
     */
    private $articlePreviewContext;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    public function __construct(
        MediaManagerInterface $mediaManager,
        FileExtensionCheckerInterface $fileExtensionChecker,
        ArticlePreviewContextInterface $articlePreviewContext,
        SettingsManagerInterface $settingsManager,
        TenantContextInterface $tenantContext
    ) {
        parent::__construct($mediaManager, $fileExtensionChecker);
        $this->articlePreviewContext = $articlePreviewContext;
        $this->settingsManager = $settingsManager;
        $this->tenantContext = $tenantContext;
    }

    protected function processImageElement(\DOMElement $imageElement, ImageRendition $rendition, ArticleMediaInterface $articleMedia): void
    {
        parent::processImageElement($imageElement, $rendition, $articleMedia);

        if ($this->articlePreviewContext->isPreview()) {
            $imageElement->setAttribute('src', $rendition->getPreviewUrl());
        }
    }

    public function applyByline(ArticleMediaInterface $articleMedia): string
    {
        $imageAuthorTemplate = $this->settingsManager->get('embedded_image_author_template', 'tenant', $this->tenantContext->getTenant());

        if (null === ($byline = $articleMedia->getByLine())) {
            return '';
        }

        return str_replace('{{ author }}', $byline, $imageAuthorTemplate);
    }
}
