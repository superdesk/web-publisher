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
use SWP\Bundle\CoreBundle\AppleNews\Component\Caption;
use SWP\Bundle\CoreBundle\AppleNews\Component\Gallery;
use SWP\Bundle\CoreBundle\AppleNews\Component\GalleryItem;
use SWP\Bundle\CoreBundle\AppleNews\Component\Intro;
use SWP\Bundle\CoreBundle\AppleNews\Component\Photo;
use SWP\Bundle\CoreBundle\AppleNews\Component\Title;
use SWP\Bundle\CoreBundle\AppleNews\Document\ArticleDocument;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentLayout;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentLayouts;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentTextStyle;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentTextStyles;
use SWP\Bundle\CoreBundle\AppleNews\Document\Layout;
use SWP\Bundle\CoreBundle\AppleNews\Document\LinkedArticle;
use SWP\Bundle\CoreBundle\AppleNews\Document\Margin;
use SWP\Bundle\CoreBundle\AppleNews\Document\Metadata;
use SWP\Bundle\CoreBundle\AppleNews\Document\TextStyle;
use SWP\Bundle\CoreBundle\AppleNews\Serializer\AppleNewsFormatSerializer;
use SWP\Bundle\CoreBundle\Factory\VersionFactory;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Routing\TenantAwareAbsoluteUrlRouter;

final class ArticleToAppleNewsFormatConverter
{
    private $serializer;

    private $router;

    private $versionFactory;

    private $articleBodyConverter;

    public function __construct(
        VersionFactory $versionFactory,
        AppleNewsFormatSerializer $serializer,
        TenantAwareAbsoluteUrlRouter $router,
        ArticleBodyToComponentsConverter $articleBodyConverter
    ) {
        $this->versionFactory = $versionFactory;
        $this->serializer = $serializer;
        $this->router = $router;
        $this->articleBodyConverter = $articleBodyConverter;
    }

    public function convert(ArticleInterface $article, TenantInterface $tenant): string
    {
        $version = $this->versionFactory->create();

        $metadata = new Metadata();
        $articleDocument = new ArticleDocument();
        $articleDocument->setTitle($article->getTitle());
        $articleDocument->setIdentifier((string) $article->getId());
        $articleDocument->setLanguage($article->getLocale());

        $articleDocument->addComponent(new Title($article->getTitle(), 'halfMarginBelowLayout'));
        $articleDocument->addComponent(new Intro($article->getLead(), 'halfMarginBelowLayout'));

        $featureMedia = $article->getFeatureMedia();

        if (null !== $featureMedia) {
            $featureMediaUrl = $this->router->generate(
                'swp_media_get',
                $tenant,
                [
                    'mediaId' => $featureMedia->getImage()->getAssetId(),
                    'extension' => $featureMedia->getImage()->getFileExtension(),
                ]
            );

            $articleDocument->addComponent(new Photo($featureMediaUrl, (string) $featureMedia->getDescription()));
            $articleDocument->addComponent(new Caption($featureMedia->getDescription(), 'marginBetweenComponents'));
            $metadata->setThumbnailURL($featureMediaUrl);
        }

        $components = $this->articleBodyConverter->convert($article->getBody());
        $components = $this->processGalleries($components, $article, $tenant);
        $links = $this->processRelatedArticles($article, $tenant);

        foreach ($components as $component) {
            $articleDocument->addComponent($component);
        }

        $articleDocument->setLayout(new Layout(20, 1024, 20, 60));

        $componentTextStyles = $this->configureComponentTextStyles();
        $articleDocument->setComponentTextStyles($componentTextStyles);

        $componentLayouts = $this->configureComponentLayouts();
        $articleDocument->setComponentLayouts($componentLayouts);

        $metadata->setAuthors($article->getAuthorsNames());

        $canonicalUrl = $this->router->generate(
            $article->getRoute()->getRouteName(),
            $tenant,
            [
                'slug' => $article->getSlug(),
            ]
        );

        $metadata->setCanonicalUrl($canonicalUrl);
        $metadata->setDateCreated($article->getCreatedAt());
        $metadata->setDatePublished($article->getPublishedAt());

        $metadata->setExcerpt($article->getLead() ?? '');

        $metadata->setGeneratorIdentifier('publisher');
        $metadata->setGeneratorName('Publisher');
        $metadata->setGeneratorVersion($version->getVersion());

        $metadata->setKeywords($article->getKeywordsNames());
        $metadata->setLinks($links);

        $articleDocument->setMetadata($metadata);

        return $this->serializer->serialize($articleDocument);
    }

    private function processGalleries(array $components, ArticleInterface $article, TenantInterface $tenant): array
    {
        if ($article->getSlideshows()->count() > 0) {
            foreach ($article->getSlideshows() as $slideshow) {
                $galleryComponent = new Gallery();
                /** @var SlideshowItemInterface $slideshowItem */
                foreach ($slideshow->getItems() as $slideshowItem) {
                    $media = $slideshowItem->getArticleMedia();
                    $caption = $media->getDescription();
                    $url = $this->router->generate(
                        'swp_media_get',
                        $tenant,
                        [
                            'mediaId' => $media->getImage()->getAssetId(),
                            'extension' => $media->getImage()->getFileExtension(),
                        ]
                    );

                    $galleryItem = new GalleryItem($url, $caption);
                    $galleryComponent->addItem($galleryItem);
                }

                $components[] = $galleryComponent;
            }
        }

        return $components;
    }

    private function processRelatedArticles(ArticleInterface $article, TenantInterface $tenant): array
    {
        $links = [];
        if ($article->getRelatedArticles()->count() > 0) {
            foreach ($article->getRelatedArticles() as $relatedArticle) {
                $relatedArticleRoute = $relatedArticle->getArticle()->getRoute();

                $url = $this->router->generate(
                    $relatedArticleRoute->getRouteName(),
                    $tenant,
                    [
                        'slug' => $relatedArticle->getArticle()->getSlug(),
                    ]
                );

                $linkedArticle = new LinkedArticle($url);
                $links[] = $linkedArticle;
            }
        }

        return $links;
    }

    private function configureComponentTextStyles(): ComponentTextStyles
    {
        $linkStyle = new TextStyle('#8a0b1f');
        $componentTextStyles = new ComponentTextStyles();
        $componentTextStylesBody = new ComponentTextStyle();
        $componentTextStylesBody->setBackgroundColor('#fff');
        $componentTextStylesBody->setFontName('IowanOldStyle-Roman');
        $componentTextStylesBody->setFontColor('#222222');
        $componentTextStylesBody->setFontSize(16);
        $componentTextStylesBody->setLineHeight(22);
        $componentTextStylesBody->setLinkStyle($linkStyle);
        $componentTextStyles->setDefault($componentTextStylesBody);

        $componentTextStylesBody = new ComponentTextStyle();
        $componentTextStylesBody->setFontName('IowanOldStyle-Roman');
        $componentTextStyles->setDefaultBody($componentTextStylesBody);

        $componentTextStylesTitle = new ComponentTextStyle();
        $componentTextStylesTitle->setFontName('DINAlternate-Bold');
        $componentTextStylesTitle->setFontSize(42);
        $componentTextStylesTitle->setLineHeight(44);
        $componentTextStylesTitle->setTextColor('#53585F');
        $componentTextStyles->setDefaultTitle($componentTextStylesTitle);

        $componentTextStylesIntro = new ComponentTextStyle();
        $componentTextStylesIntro->setFontName('DINAlternate-Bold');
        $componentTextStylesIntro->setFontSize(18);
        $componentTextStylesIntro->setLineHeight(22);
        $componentTextStylesIntro->setTextColor('#A6AAA9');
        $componentTextStyles->setDefaultIntro($componentTextStylesIntro);

        return $componentTextStyles;
    }

    private function configureComponentLayouts(): ComponentLayouts
    {
        $componentLayouts = new ComponentLayouts();
        $componentLayout = new ComponentLayout();
        $componentLayout->setColumnSpan(14);
        $componentLayout->setColumnStart(0);
        $componentLayout->setMargin(new Margin(12));
        $componentLayouts->setHalfMarginBelowLayout($componentLayout);

        $componentLayout = new ComponentLayout();
        $componentLayout->setColumnSpan(14);
        $componentLayout->setColumnStart(0);
        $componentLayout->setMargin(new Margin(12, 12));
        $componentLayouts->setMarginBetweenComponents($componentLayout);

        return $componentLayouts;
    }
}
