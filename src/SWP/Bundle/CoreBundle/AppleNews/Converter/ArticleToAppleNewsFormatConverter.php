<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Converter;

use SWP\Bundle\ContentBundle\Model\SlideshowItemInterface;
use SWP\Bundle\CoreBundle\AppleNews\Component\Body;
use SWP\Bundle\CoreBundle\AppleNews\Component\EmbedWebVideo;
use SWP\Bundle\CoreBundle\AppleNews\Component\Figure;
use SWP\Bundle\CoreBundle\AppleNews\Component\Gallery;
use SWP\Bundle\CoreBundle\AppleNews\Component\GalleryItem;
use SWP\Bundle\CoreBundle\AppleNews\Component\Heading;
use SWP\Bundle\CoreBundle\AppleNews\Document\ArticleDocument;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentTextStyle;
use SWP\Bundle\CoreBundle\AppleNews\Document\ComponentTextStyles;
use SWP\Bundle\CoreBundle\AppleNews\Document\Layout;
use SWP\Bundle\CoreBundle\AppleNews\Document\LinkedArticle;
use SWP\Bundle\CoreBundle\AppleNews\Document\Metadata;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ArticleToAppleNewsFormatConverter
{
    private $serializer;

    private $router;

    public function __construct(SerializerInterface $serializer, RouterInterface $router)
    {
        $this->serializer = $serializer;
        $this->router = $router;
    }

    public function convert(ArticleInterface $article): string
    {
        $articleDocument = new ArticleDocument();
        $articleDocument->setTitle($article->getTitle());
        $articleDocument->setIdentifier((string) $article->getId());
        $articleDocument->setLanguage($article->getLocale());

        $components = [];
        $components = $this->processArticleBody($components, $article->getBody());
        $components = $this->processGalleries($components, $article);
        $links = $this->processRelatedArticles($article);

        // handle twitter embeds
        // handle facebook embeds and other embeds

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
        $metadata->setGeneratorVersion('2.1');

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

        return $this->serializer->serialize($articleDocument, 'json');
    }

    public function stripHtmlTags(string $html): string
    {
        $html = preg_replace('/<script.*>.*<\/script>/isU', '', $html);

        return $html;
    }

    private function processArticleBody(array $components = [], string $html): array
    {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$this->stripHtmlTags($html));
        $document->encoding = 'UTF-8';
        libxml_clear_errors();

        /** @var \DOMNodeList $body */
        if (!($body = $document->getElementsByTagName('body')->item(0))) {
            throw new \InvalidArgumentException('Invalid HTML was provided');
        }

        foreach ($body->childNodes as $node) {
            switch ($node->nodeName) {
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                    if ('' !== $node->textContent) {
                        $level = substr($node->nodeName, 1);
                        $components[] = new Heading($node->textContent, (int) $level);
                    }

                    break;
                case 'p':
                    if ('' !== $node->textContent) {
                        $components[] = new Body($node->textContent);
                    }

                    break;

                case 'figure':
                    $src = $node->getElementsByTagName('img')
                        ->item(0)
                        ->getAttribute('src');

                    $caption = $node->getElementsByTagName('figcaption')
                        ->item(0)
                        ->textContent;

                    $components[] = new Figure($src, $caption);

                    break;
                case 'div':
                    if ($node->hasAttribute('class')) {
                        $webVideoUrl = $node->getElementsByTagName('iframe')
                            ->item(0)
                            ->getAttribute('src');

                        $url = str_replace('\"', '', $webVideoUrl);
                        $components[] = new EmbedWebVideo($url);
                    }

                    break;
            }
        }

        return $components;
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
