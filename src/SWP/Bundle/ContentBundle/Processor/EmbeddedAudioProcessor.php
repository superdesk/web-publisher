<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Processor;

use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;

final class EmbeddedAudioProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    public function __construct(FileExtensionCheckerInterface $fileExtensionChecker)
    {
        $this->fileExtensionChecker = $fileExtensionChecker;
    }

    public function process(ArticleInterface $article, ArticleMediaInterface $articleMedia): void
    {
        // TODO: Implement process() method.
    }

    public function supports(string $type): bool
    {
        return $this->fileExtensionChecker->isAudio($type);
    }
}
