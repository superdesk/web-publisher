<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig;

use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Processor\EmbeddedImageProcessor;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ArticleBodyExtension extends AbstractExtension
{
    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    public function __construct(MediaManagerInterface $mediaManager, FileExtensionCheckerInterface $fileExtensionChecker)
    {
        $this->mediaManager = $mediaManager;
        $this->fileExtensionChecker = $fileExtensionChecker;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setRendition', [$this, 'setRendition']),
        ];
    }

    public function setRendition(Meta $articleMeta, string $renditionName): void
    {
        if (!$articleMeta->getValues() instanceof ArticleInterface) {
            return;
        }

        /** @var ArticleInterface $article */
        $article = $articleMeta->getValues();
        $embeddedImageProcessor = new EmbeddedImageProcessor($this->mediaManager, $this->fileExtensionChecker);
        $embeddedImageProcessor->setDefaultImageRendition($renditionName);

        foreach ($article->getMedia() as $media) {
            $embeddedImageProcessor->process($article, $media);
        }
    }
}
