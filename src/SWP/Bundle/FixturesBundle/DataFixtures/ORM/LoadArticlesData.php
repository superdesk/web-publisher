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
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\AuthorMedia;
use SWP\Bundle\CoreBundle\Model\Image;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\ArticleEventInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\FixturesBundle\Faker\Provider\ArticleDataProvider;
use SWP\Component\Bridge\Model\ExternalDataInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadArticlesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
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
        $mediaManager = $this->container->get('swp_content_bundle.manager.media');
        if (null === $tenantContext->getTenant()) {
            $tenantContext->setTenant(
                $this->container->get('swp.repository.tenant')->findOneByCode('123abc')
            );
        }
        $mediaManager->setTenantContext($tenantContext);

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

        if ('test' !== $env) {
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

                    $randNumber = rand(1, 9);
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

                        $mediaId = uniqid();
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
                        $imageRendition->setHeight($rendition['height']);
                        $imageRendition->setWidth($rendition['width']);
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
                    'pageViewsDates' => [
                        '-1 day' => 3,
                        '-2 days' => 2,
                        '-3 days' => 3,
                        '-4 days' => 1,
                        '-5 days' => 6,
                        '-6 days' => 1,
                        '-7 days' => 4,
                    ],
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
                ],
                [
                    'title' => 'Test news sports article',
                    'content' => 'Test news sports article content',
                    'route' => 'sports',
                    'locale' => 'en',
                    'pageViews' => 30,
                    'pageViewsDates' => [
                        '-1 day' => 3,
                        '-2 days' => 2,
                        '-3 days' => 8,
                        '-4 days' => 1,
                        '-5 days' => 6,
                        '-6 days' => 6,
                        '-7 days' => 4,
                    ],
                    'authors' => [
                        'Test Person',
                    ],
                    'sources' => ['Reuters', 'AFP'],
                    'external' => [
                        'webcode' => '+jxux6',
                        'extra data' => 'extra value',
                    ],
                ],
                [
                    'title' => 'Test article',
                    'content' => 'Test article content',
                    'route' => 'news',
                    'locale' => 'en',
                    'pageViews' => 10,
                    'pageViewsDates' => [
                        '-1 day' => 3,
                        '-2 days' => 3,
                        '-4 days' => 1,
                        '-5 days' => 1,
                        '-6 days' => 1,
                        '-7 days' => 1,
                    ],
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
                    'pageViewsDates' => [
                        '- 7 days' => 5,
                    ],
                    'authors' => [
                        'John Doe Second',
                    ],
                    'sources' => ['Reuters', 'AAP'],
                ],
                [
                    'title' => 'Features client1',
                    'content' => 'Features client1 content',
                    'route' => 'articles/features',
                    'locale' => 'en',
                    'pageViews' => 0,
                    'pageViewsDates' => [],
                    'authors' => [
                        'Test Person',
                    ],
                    'sources' => ['Forbes', 'AFP'],
                    'external' => [
                        'articleNumber' => '64525',
                    ],
                ],
            ],
        ];

        if (isset($articles[$env])) {
            $articleService = $this->container->get('swp.service.article');
            $sourcesFactory = $this->container->get('swp.factory.article_source');
            $articleSourcesService = $this->container->get('swp.service.article_source');
            foreach ($articles[$env] as $articleData) {
                /** @var ArticleInterface $article */
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                $article->setKeywords($articleDataProvider->articleKeywords());
                $manager->persist($article);

                if (isset($articleData['extra'])) {
                    $article->setExtra($articleData['extra']);
                }

                if (isset($articleData['authors'])) {
                    foreach ($articleData['authors'] as $authorName) {
                        $author = new ArticleAuthor();
                        $author->setBiography('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibu');
                        $author->setRole('Writer');
                        $author->setName($authorName);
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

                $articleStatistics = $this->createArticleStatistics($articleData['pageViews'], $articleData['pageViewsDates'], $article, $manager);
                $manager->persist($articleStatistics);
                $manager->persist($package);
                $article->setPackage($package);
                $articleService->publish($article);

                $this->addReference($article->getSlug(), $article);
            }

            $manager->flush();
        }
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

    private function createArticleStatistics(int $pageViewsNumber, array $pageViewsDates, ArticleInterface $article, ObjectManager $manager): ArticleStatisticsInterface
    {
        /** @var ArticleStatisticsInterface $articleStatistics */
        $articleStatistics = $this->container->get('swp.factory.article_statistics')->create();
        $articleStatistics->setArticle($article);
        $articleStatistics->setPageViewsNumber($pageViewsNumber);

        $count = 0;
        foreach ($pageViewsDates as $dateValue => $number) {
            for ($i = $number; $i > 0; --$i) {
                $articleEvent = new ArticleEvent();
                $articleEvent->setArticleStatistics($articleStatistics);
                $articleEvent->setAction(ArticleEventInterface::ACTION_PAGEVIEW);
                $date = new \DateTime();
                $date->modify($dateValue);
                $date->setTime(mt_rand(0, 23), (int) str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT));
                $articleEvent->setCreatedAt($date);
                $manager->persist($articleEvent);
                ++$count;
            }
        }

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

        $newImage = imagecreatetruecolor($renditionWidth, $renditionHeight);

        imagecopyresampled($newImage,
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
