<?php

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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadArticlesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
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

    public function loadRoutes($env, $manager)
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
            ],
        ];

        $routeService = $this->container->get('swp.service.route');

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

            $route = $routeService->fillRoute($route);

            $manager->persist($route);
        }

        $manager->flush();
    }

    /**
     * Sets articles manually (not via Alice) for test env due to fatal error:
     * Method PHPCRProxies\__CG__\Doctrine\ODM\PHPCR\Document\Generic::__toString() must not throw an exception.
     */
    public function loadArticles($env, ObjectManager $manager)
    {
        if ($env !== 'test') {
            $this->loadFixtures([
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/package.yml',
            ],
                $manager,
                [
                    'providers' => [$this],
                ]
            );

            $articles = $this->loadFixtures([
                    '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/article.yml',
                ],
                $manager,
                [
                    'providers' => [$this],
                ],
                true
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

            foreach ($articles as $article) {
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
                ],
                [
                    'title' => 'Test article',
                    'content' => 'Test article content',
                    'route' => 'news',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Features',
                    'content' => 'Features content',
                    'route' => 'news',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Features client1',
                    'content' => 'Features client1 content',
                    'route' => 'articles/features',
                    'locale' => 'en',
                ],
            ],
        ];

        if (isset($articles[$env])) {
            $articleService = $this->container->get('swp.service.article');
            foreach ($articles[$env] as $articleData) {
                /** @var ArticleInterface $article */
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                $article->setKeywords($this->articleKeywords());
                $package = $this->createPackage($articleData);
                $manager->persist($package);
                $article->setPackage($package);
                $manager->persist($article);
                $articleService->publish($article);

                $this->addReference($article->getSlug(), $article);
            }

            $manager->flush();
        }
    }

    private function createPackage(array $articleData)
    {
        /** @var PackageInterface $package */
        $package = $this->container->get('swp.factory.package')->create();
        $package->setHeadline($articleData['title']);
        $package->setType('text');
        $package->setPubStatus('usable');
        $package->setGuid($this->container->get('swp_multi_tenancy.random_string_generator')->generate(10));
        $package->setLanguage('en');
        $package->setUrgency(1);
        $package->setPriority(1);
        $package->setVersion(1);

        return $package;
    }

    /**
     * @return array
     */
    public function articleKeywords()
    {
        $keywords = [
            'city',
            'traffic',
            'car',
            'news',
            'building',
        ];

        shuffle($keywords);

        return $keywords;
    }

    /**
     * Article example metadata.
     *
     * @return array
     */
    public function articleMetadata()
    {
        $authors = [
            'Sarrah Staffwriter',
            'John Smith',
            'Test Persona',
            'Jane Stockwriter',
            'James Q. Reporter',
            'Karen Ruhiger',
            'George Langsamer',
        ];

        return [
            'located' => 'Sydney',
            'byline' => $authors[array_rand($authors)],
            'place' => [
                [
                    'qcode' => 'AUS',
                    'world_region' => 'Rest Of World',
                ], [
                    'qcode' => 'EUR',
                    'world_region' => 'Europe',
                ],
            ],
        ];
    }

    private function cropAndResizeImage($fakeImage, array $rendition, $targetFile)
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
            0 - ($newImageWidth - $renditionWidth) / 2,
            0 - ($newImageHeight - $renditionHeight) / 2,
            0,
            0,
            $newImageWidth,
            $newImageHeight,
            $width,
            $height);
        imagejpeg($newImage, $targetFile, 80);

        imagedestroy($newImage);
        unset($image);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
