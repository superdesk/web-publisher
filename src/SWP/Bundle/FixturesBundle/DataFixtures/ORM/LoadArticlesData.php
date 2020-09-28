<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\AuthorMedia;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\RelatedArticle;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\Image;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\FixturesBundle\Faker\Provider\ArticleDataProvider;
use SWP\Component\Bridge\Model\ExternalDataInterface;
use SWP\Component\Bridge\Model\Rendition;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadArticlesData extends AbstractFixture implements OrderedFixtureInterface
{
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $env = $this->getEnvironment();

        $tenantContext = $this->container->get('swp_multi_tenancy.tenant_context');
        if (null === $tenantContext->getTenant()) {
            $tenantContext->setTenant(
                $this->container->get('swp.repository.tenant')->findOneByCode('123abc')
            );
        }

        $this->loadRoutes($env, $manager);
        $this->loadArticles($env, $manager);

        $manager->flush();
    }

    public function loadRoutes($env, $manager): void
    {
        $routes = [
            'dev' => [
                [
                    'name' => 'politics',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'templateName' => 'category.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'business',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'templateName' => 'category.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'scitech',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'templateName' => 'category.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'health',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'templateName' => 'category.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'entertainment',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'templateName' => 'category.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'sports',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'templateName' => 'category.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'about',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                    'articlesTemplateName' => 'page.html.twig',
                ],
                [
                    'name' => 'home',
                    'type' => 'content',
                    'templateName' => 'index.html.twig',
                ],
            ],
            'test' => [
                [
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9*\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                ],
                [
                    'name' => 'articles',
                    'type' => 'content',
                ],
                [
                    'name' => 'articles/features',
                    'type' => 'content',
                ],
                [
                    'name' => 'sports',
                    'type' => 'collection',
                    'parentName' => 'news',
                ],
            ],
        ];

        $routeService = $this->container->get('swp.service.route');

        $persistedRoutes = [];
        foreach ($routes[$env] as $routeData) {
            /** @var RouteInterface $route */
            $route = $this->container->get('swp.factory.route')->create();
            $route->setName($routeData['name']);
            $route->setType($routeData['type']);

            if (isset($routeData['cacheTimeInSeconds'])) {
                $route->setCacheTimeInSeconds($routeData['cacheTimeInSeconds']);
            }

            if (isset($routeData['templateName'])) {
                $route->setTemplateName($routeData['templateName']);
            }

            if (isset($routeData['articlesTemplateName'])) {
                $route->setArticlesTemplateName($routeData['articlesTemplateName']);
            }

            if (isset($routeData['parentName']) && isset($persistedRoutes[$routeData['parentName']])) {
                $route->setParent($persistedRoutes[$routeData['parentName']]);
            }

            $route = $routeService->fillRoute($route);

            $manager->persist($route);
            $persistedRoutes[$route->getName()] = $route;
        }

        $manager->flush();
    }

    public function loadArticles($env, ObjectManager $manager): void
    {
        $articleDataProvider = $this->container->get(ArticleDataProvider::class);

        if ('dev' === $env) {
            $data = $this->loadFixtures([
                    '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/package.yml',
                    '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/article.yml',
                ]
            );

            $renditions = [
                'original' => [],
                '770x515' => [
                    'width' => '770',
                    'height' => '515',
                ],
                '478x326' => [
                    'width' => '478',
                    'height' => '326',
                ],
                '480x480' => [
                    'width' => '480',
                    'height' => '480',
                ],
                '960x480' => [
                    'width' => '960',
                    'height' => '480',
                ],
                '600x360' => [
                    'width' => '600',
                    'height' => '360',
                ],
                '400x240' => [
                    'width' => '400',
                    'height' => '240',
                ],
                '1000x1000' => [
                    'width' => '1000',
                    'height' => '1000',
                ],
                '610x380' => [
                    'width' => '610',
                    'height' => '380',
                ],
                '1250x600' => [
                    'width' => '1250',
                    'height' => '600',
                ],
            ];

            $mediaManager = $this->container->get('swp_content_bundle.manager.media');

            foreach ($renditions as $key => $rendition) {
                if ('original' === $key) {
                    continue;
                }

                for ($i = 1; $i <= 9; ++$i) {
                    $filename = '/tmp/'.$i.'org'.$key.'.jpg';
                    if (file_exists($filename)) {
                        continue;
                    }

                    $fakeImage = __DIR__.'/../../Resources/assets/'.$i.'org.jpg';
                    $this->cropAndResizeImage($fakeImage, $rendition, $filename);
                }
            }

            foreach ((array) $data as $article) {
                if (!$article instanceof \SWP\Bundle\CoreBundle\Model\ArticleInterface) {
                    continue;
                }

                // randomly create two media (images) for each of the article
                for ($i = 0; $i < 2; ++$i) {
                    // create Media
                    $articleMediaClass = $this->container->getParameter('swp.model.media.class');
                    $articleMedia = new $articleMediaClass();
                    $articleMedia->setArticle($article);
                    $articleMedia->setKey('embedded'.uniqid());
                    $articleMedia->setBody('This is very nice image caption...');
                    $articleMedia->setByLine('By Best Editor');
                    $articleMedia->setLocated('Porto');
                    $articleMedia->setDescription('Media description');
                    $articleMedia->setUsageTerms('Some super open terms');
                    $articleMedia->setMimetype('image/jpeg');
                    $manager->persist($articleMedia);

                    $randNumber = random_int(1, 9);
                    /* @var $rendition Rendition */
                    foreach ($renditions as $key => $rendition) {
                        if ('original' === $key) {
                            $fakeImage = __DIR__.'/../../Resources/assets/'.$randNumber.'org.jpg';
                            list($width, $height) = getimagesize($fakeImage);
                            $rendition['height'] = $height;
                            $rendition['width'] = $width;
                        } else {
                            $fakeImage = '/tmp/'.$randNumber.'org'.$key.'.jpg';
                        }

                        $mediaId = uniqid('', true);
                        $uploadedFile = new UploadedFile(
                            $fakeImage,
                            $mediaId,
                            'image/jpeg',
                            filesize($fakeImage),
                            null,
                            true
                        );
                        $image = $mediaManager->handleUploadedFile($uploadedFile, $mediaId);
                        $articleMedia->setImage($image);

                        $imageRendition = new ImageRendition();
                        $imageRendition->setImage($image);
                        $imageRendition->setHeight((int) $rendition['height']);
                        $imageRendition->setWidth((int) $rendition['width']);
                        $imageRendition->setName($key);
                        $imageRendition->setMedia($articleMedia);
                        $articleMedia->addRendition($imageRendition);
                        $manager->persist($imageRendition);
                    }
                }
            }
        }

        $articles = [
            'test' => [
                [
                    'title' => 'Test news article',
                    'content' => 'Test news article content',
                    'route' => 'news',
                    'locale' => 'en',
                    'pageViews' => 20,
                    'extra' => [
                        'custom-field' => 'my custom field',
                    ],
                    'authors' => [
                        'Tom',
                    ],
                    'sources' => ['Forbes', 'AAP'],
                    'external' => [
                        'webcode' => '+jxuk9',
                    ],
                    'commentsCount' => 20,
                ],
                [
                    'title' => 'Test news sports article',
                    'content' => 'Test news sports article content',
                    'route' => 'sports',
                    'locale' => 'en',
                    'pageViews' => 30,
                    'authors' => [
                        'Test Person',
                    ],
                    'sources' => ['Reuters', 'AFP'],
                    'external' => [
                        'webcode' => '+jxux6',
                        'extra data' => 'extra value',
                    ],
                    'extra' => [
                        'articleNumber' => '1919',
                    ],
                    'commentsCount' => 34,
                ],
                [
                    'title' => 'Test article',
                    'content' => 'Test article content',
                    'route' => 'news',
                    'locale' => 'en',
                    'pageViews' => 10,
                    'authors' => [
                        'John Doe',
                    ],
                    'sources' => ['Forbes', 'AAP'],
                    'external' => [
                        'articleNumber' => '10242',
                    ],
                ],
                [
                    'title' => 'Features',
                    'content' => 'Features content',
                    'route' => 'news',
                    'locale' => 'en',
                    'pageViews' => 5,
                    'authors' => [
                        'John Doe Second',
                    ],
                    'sources' => ['Reuters', 'AAP'],
                    'commentsCount' => 5,
                ],
                [
                    'title' => 'Features client1',
                    'content' => 'Features client1 content',
                    'route' => 'articles/features',
                    'locale' => 'en',
                    'pageViews' => 0,
                    'publishedAt' => (new \DateTime())->modify('-3 days'),
                    'authors' => [
                        'Test Person',
                    ],
                    'sources' => ['Forbes', 'AFP'],
                    'external' => [
                        'articleNumber' => '64525',
                    ],
                    'commentsCount' => 10,
                ],
            ],
        ];

        if (isset($articles[$env])) {
            $articleService = $this->container->get('swp.service.article');
            $sourcesFactory = $this->container->get('swp.factory.article_source');
            $articleSourcesService = $this->container->get('swp.service.article_source');

            $persistedKeywords = $articleDataProvider->articleKeywords();
            $manager->flush();

            foreach ($articles[$env] as $articleData) {
                /** @var \SWP\Bundle\CoreBundle\Model\ArticleInterface $article */
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                $article->setMetadata([
                    'located' => 'Sydney',
                    'byline' => 'Jhon Doe',
                    'place' => [
                        [
                            'qcode' => 'AUS',
                            'world_region' => 'Rest Of World',
                        ], [
                            'qcode' => 'EUR',
                            'world_region' => 'Europe',
                        ],
                    ],
                ]);
                if (isset($articleData['publishedAt'])) {
                    $article->setPublishedAt($articleData['publishedAt']);
                }

                $manager->persist($article);
                foreach ($persistedKeywords as $index => $persistedKeyword) {
                    if ($index < 3) {
                        $article->addKeyword($persistedKeyword);
                    }
                }
                $article->addKeyword($persistedKeywords[rand(3, 4)]);

                if (isset($articleData['extra'])) {
                    $article->setExtraFields($articleData['extra']);
                }

                if (isset($articleData['commentsCount'])) {
                    $article->setCommentsCount($articleData['commentsCount']);
                }

                if (isset($articleData['authors'])) {
                    foreach ($articleData['authors'] as $authorName) {
                        $author = new ArticleAuthor();
                        $author->setBiography('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibu');
                        $author->setRole('Writer');
                        $author->setName($authorName);
                        $author->setTwitter('@superdeskman');
                        $author->setFacebook('superdeskman');
                        $author->setInstagram('superdeskman');
                        $image = new Image();
                        $image->setAssetId($author->getSlug());
                        $image->setFileExtension('jpg');
                        $manager->persist($image);
                        $authorMedia = new AuthorMedia('avatar', $author, $image);
                        $manager->persist($authorMedia);
                        $author->setAvatar($authorMedia);
                        $article->addAuthor($author);
                    }
                }

                if (isset($articleData['sources'])) {
                    foreach ((array) $articleData['sources'] as $source) {
                        $articleSource = $sourcesFactory->create();
                        $articleSource->setName($source);
                        $article->addSourceReference($articleSourcesService->getArticleSourceReference($article, $articleSource));
                    }
                }

                $package = $this->createPackage($articleData);
                if (isset($articleData['external'])) {
                    foreach ($articleData['external'] as $dataKey => $dataValue) {
                        /** @var ExternalDataInterface $externalData */
                        $externalData = $this->container->get('swp.factory.external_data')->create();
                        $externalData->setKey($dataKey);
                        $externalData->setValue($dataValue);
                        $externalData->setPackage($package);
                        $manager->persist($externalData);
                    }
                }

                $articleStatistics = $this->createArticleStatistics($articleData['pageViews'], $article);
                $manager->persist($articleStatistics);
                $manager->persist($package);
                $article->setPackage($package);
                $articleService->publish($article);

                $this->addReference($article->getSlug(), $article);
            }

            $manager->flush();

            $article = $this->container->get('swp.repository.article')->findOneById(1);
            $relatedArticle1 = $this->container->get('swp.repository.article')->findOneById(2);
            $relatedArticle2 = $this->container->get('swp.repository.article')->findOneById(3);

            $related1 = new RelatedArticle();
            $related1->setArticle($relatedArticle1);

            $related2 = new RelatedArticle();
            $related2->setArticle($relatedArticle2);

            $article->addRelatedArticle($related1);
            $article->addRelatedArticle($related2);
        }

        $manager->flush();
    }

    private function createPackage(array $articleData): PackageInterface
    {
        /** @var PackageInterface $package */
        $package = $this->container->get('swp.factory.package')->create();
        $package->setHeadline($articleData['title']);
        $slug = str_replace('\'', '-', $package->getHeadline());
        $package->setSlugline(Transliterator::transliterate($slug));
        $package->setType('text');
        $package->setPubStatus('usable');
        $package->setGuid($this->container->get('swp_multi_tenancy.random_string_generator')->generate(10));
        $package->setLanguage('en');
        $package->setUrgency(1);
        $package->setPriority(1);
        $package->setVersion(1);

        return $package;
    }

    private function createArticleStatistics(int $pageViewsNumber, ArticleInterface $article): ArticleStatisticsInterface
    {
        /** @var ArticleStatisticsInterface $articleStatistics */
        $articleStatistics = $this->container->get('swp.factory.article_statistics')->create();
        $articleStatistics->setArticle($article);
        $articleStatistics->setPageViewsNumber($pageViewsNumber);

        return $articleStatistics;
    }

    private function cropAndResizeImage($fakeImage, array $rendition, $targetFile): void
    {
        $image = imagecreatefromjpeg($fakeImage);
        list($width, $height) = getimagesize($fakeImage);

        $renditionWidth = (int) $rendition['width'];
        $renditionHeight = (int) $rendition['height'];

        $aspectRatio = $width / $height;
        $newImageAspectRatio = $renditionWidth / $renditionHeight;

        if ($aspectRatio >= $newImageAspectRatio) {
            $newImageHeight = $renditionHeight;
            $newImageWidth = $width / ($height / $renditionHeight);
        } else {
            $newImageWidth = $renditionWidth;
            $newImageHeight = $height / ($width / $renditionWidth);
        }

        $newImage = \imagecreatetruecolor($renditionWidth, $renditionHeight);

        \imagecopyresampled($newImage,
            $image,
            0 - (int) (($newImageWidth - $renditionWidth) / 2),
            0 - (int) (($newImageHeight - $renditionHeight) / 2),
            0,
            0,
            (int) $newImageWidth,
            (int) $newImageHeight,
            $width,
            $height);
        imagejpeg($newImage, $targetFile, 80);

        imagedestroy($newImage);
        unset($image);
    }

    public function getOrder(): int
    {
        return 1;
    }
}
