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

    /**
     * @var int
     */
    private $paragraphChars;

    public function __construct(MediaManagerInterface $mediaManager, FileExtensionCheckerInterface $fileExtensionChecker, $paragraphChars)
    {
        $this->mediaManager = $mediaManager;
        $this->fileExtensionChecker = $fileExtensionChecker;
        $this->paragraphChars = $paragraphChars;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setRendition', [$this, 'setRendition']),
            new TwigFunction('setInlineAds', [$this, 'setInlineAds']),
        ];
    }

    public function setInlineAds(string $body): array
    {
        # split the body into paragraphs
        $paragraphs = explode("</p>", $body);

        $tmp = [];
        $splittedBody = [];
        $parLength = 0;
        $paragraphChars = $this->paragraphChars;
        foreach ($paragraphs as $key => $paragraph) {
            $paragraph .= '</p>';
            $safeToAdd = true;

            #check if the next element is a non empty paragraph
            if (isset($paragraphs[$key+1]) && strlen($paragraphs[$key+1]) && $key < sizeof($paragraphs)) {
                $bodyDOM2 =  new \DOMDocument();
                @$bodyDOM2->loadHTML((mb_convert_encoding($paragraphs[$key+1], 'HTML-ENTITIES', 'UTF-8')));
                $pars2 = $bodyDOM2->getElementsByTagName('*')->item(2);
                if ($pars2->tagName != 'p' || ($pars2->tagName == 'p' && strlen($pars2->textContent) == 0)) {
                    $safeToAdd = false;
                }
            }

            if (strlen($paragraph) > 0) {
                $bodyDOM =  new \DOMDocument();
                @$bodyDOM->loadHTML((mb_convert_encoding($paragraph, 'HTML-ENTITIES', 'UTF-8')));
                $pars = $bodyDOM->getElementsByTagName('p');

                # loop through all the p tags inside the array
                foreach ($pars as $node) {
                    $parLength += strlen($node->textContent);

                    if ($parLength >= $paragraphChars && $safeToAdd) {
                        $tmp[] = $paragraph;
                             
                        $parHolder = '';
                        foreach ($tmp as $tmpParagraph) {
                            $parHolder .= $tmpParagraph;
                        }
                        $splittedBody[] = $parHolder;
                        $tmp = [];
                        $parLength = 0;
                    } else {
                        $tmp[] = $paragraph;
                    }
                }
            }
        }
  
        if (!empty($tmp)) {
            $parHolder = '';
            foreach ($tmp as $tmpParagraph) {
                $parHolder .= $tmpParagraph;
            }
            $splittedBody[] = $parHolder;
        }

        return $splittedBody;
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
