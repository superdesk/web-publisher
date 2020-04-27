<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Converter;

use SWP\Bundle\ContentBundle\Model\SlideshowItemInterface;
use SWP\Bundle\CoreBundle\AppleNews\Component\Gallery;
use SWP\Bundle\CoreBundle\AppleNews\Component\GalleryItem;
use SWP\Bundle\CoreBundle\AppleNews\Document\ArticleDocument;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentTextStyle;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentTextStyles;
use SWP\Bundle\CoreBundle\AppleNews\Document\Layout;
use SWP\Bundle\CoreBundle\AppleNews\Document\LinkedArticle;
use SWP\Bundle\CoreBundle\AppleNews\Document\Metadata;
use SWP\Bundle\CoreBundle\Factory\VersionFactory;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ArticleToAppleNewsFormatConverter
{
    private $serializer;

    private $router;

    private $versionFactory;

    private $articleBodyConverter;

    public function __construct(
        VersionFactory $versionFactory,
        SerializerInterface $serializer,
        RouterInterface $router,
        ArticleBodyToComponentsConverter $articleBodyConverter
    ) {
        $this->versionFactory = $versionFactory;
        $this->serializer = $serializer;
        $this->router = $router;
        $this->articleBodyConverter = $articleBodyConverter;
    }

    public function convert(ArticleInterface $article): string
    {
        $version = $this->versionFactory->create();

        $articleDocument = new ArticleDocument();
        $articleDocument->setTitle($article->getTitle());
        $articleDocument->setIdentifier((string) $article->getId());
        $articleDocument->setLanguage($article->getLocale());

        $components = $this->articleBodyConverter->convert($article->getBody());
        $components = $this->processGalleries($components, $article);
        $links = $this->processRelatedArticles($article);

        foreach ($components as $component) {
            $articleDocument->addComponent($component);
        }

        $articleDocument->setLayout(new Layout(20, 1024, 20, 60));

        $componentTextStyles = new ComponentTextStyles();
        $componentTextStyles->setDefault(new ComponentTextStyle('#000', 'HelveticaNeue'));
        $articleDocument->setComponentTextStyles($componentTextStyles);

        $metadata = new Metadata();
        $metadata->setAuthors($article->getAuthorsNames());

        $canonicalUrl = $this->router->generate($article->getRoute()->getRouteName(), [
            'slug' => $article->getSlug(),
        ], RouterInterface::ABSOLUTE_URL);
        $metadata->setCanonicalUrl($canonicalUrl);
        $metadata->setDateCreated($article->getCreatedAt());
        $metadata->setDatePublished($article->getPublishedAt());

        $metadata->setExcerpt($article->getLead() ?? '');

        $metadata->setGeneratorIdentifier('publisher');
        $metadata->setGeneratorName('Publisher');
        $metadata->setGeneratorVersion($version->getVersion());

        $metadata->setKeywords($article->getKeywordsNames());
        $metadata->setLinks($links);

        $featureMedia = $article->getFeatureMedia();
        if (null !== $featureMedia) {
            $featureMediaUrl = $this->router->generate('swp_media_get', [
                'mediaId' => $featureMedia->getImage()->getAssetId(),
                'extension' => $featureMedia->getImage()->getFileExtension(),
            ], RouterInterface::ABSOLUTE_URL);

            $metadata->setThumbnailURL($featureMediaUrl);
        }

        $articleDocument->setMetadata($metadata);

        return str_replace('"url":', '"URL":', $this->serializer->serialize($articleDocument, 'json'));
    }

    private function processGalleries(array $components, ArticleInterface $article): array
    {
        if ($article->getSlideshows()->count() > 0) {
            foreach ($article->getSlideshows() as $slideshow) {
                $galleryComponent = new Gallery();
                /** @var SlideshowItemInterface $slideshowItem */
                foreach ($slideshow->getItems() as $slideshowItem) {
                    $media = $slideshowItem->getArticleMedia();
                    $caption = $media->getDescription();
                    $url = $this->router->generate('swp_media_get', [
                        'mediaId' => $media->getImage()->getAssetId(),
                        'extension' => $media->getImage()->getFileExtension(),
                    ], RouterInterface::ABSOLUTE_URL);

                    $galleryItem = new GalleryItem($url, $caption);
                    $galleryComponent->addItem($galleryItem);
                }

                $components[] = $galleryComponent;
            }
        }

        return $components;
    }

    private function processRelatedArticles(ArticleInterface $article): array
    {
        $links = [];
        if ($article->getRelatedArticles()->count() > 0) {
            foreach ($article->getRelatedArticles() as $relatedArticle) {
                $relatedArticleRoute = $relatedArticle->getArticle()->getRoute();
                $url = $this->router->generate($relatedArticleRoute->getRouteName(), [
                    'slug' => $relatedArticle->getArticle()->getSlug(),
                ], RouterInterface::ABSOLUTE_URL);
                $linkedArticle = new LinkedArticle($url);
                $links[] = $linkedArticle;
            }
        }

        return $links;
    }
}
