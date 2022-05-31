<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadArticlesMediaData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    private $manager;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadArticles($manager);

        $manager->flush();
    }

    public function loadArticles($manager)
    {
        $articles = [
            [
                'title' => 'Test news article',
                'content' => 'Test news article content',
                'locale' => 'en',
            ],
        ];

        $articleMedias = [
            'embedded6358005131' => [
                'original' => [
                    'width' => '1200',
                    'height' => '900',
                    'media' => '20160905140916/12345678987654321a',
                ],
                '16-9' => [
                    'width' => '1079',
                    'height' => '720',
                    'media' => '20160905140916/12345678987654321c',
                ],
                '4-3' => [
                    'width' => '800',
                    'height' => '533',
                    'media' => '20160905140916/12345678987654321e',
                ],
            ],
            'embedded5366428123' => [
                '600x300' => [
                    'width' => '400',
                    'height' => '300',
                    'media' => '58512c10c3a5be49fad39a2d',
                ],
                'viewImage' => [
                    'width' => '640',
                    'height' => '480',
                    'media' => '58512be5c3a5be49fdca1172',
                ],
                'thumbnail' => [
                    'width' => '160',
                    'height' => '120',
                    'media' => '58512be5c3a5be49fdca116c',
                ],
                'original' => [
                    'width' => '1200',
                    'height' => '900',
                    'media' => '58512be4c3a5be49fdca1168',
                ],
                'baseImage' => [
                    'width' => '1400',
                    'height' => '1050',
                    'media' => '58512be5c3a5be49fdca1170',
                ],
                '777x600' => [
                    'width' => '777',
                    'height' => '582',
                    'media' => '58512c10c3a5be49fad39a29',
                ],
            ],
            'embedded11331114891' => [
                '600x300' => [
                    'width' => '451',
                    'height' => '300',
                    'media' => '58512c44c3a5be49f3529d98',
                ],
                'viewImage' => [
                    'width' => '640',
                    'height' => '425',
                    'media' => '58512be7c3a5be49fdca1184',
                ],
                'thumbnail' => [
                    'width' => '180',
                    'height' => '120',
                    'media' => '58512be6c3a5be49fdca117e',
                ],
                'original' => [
                    'width' => '1200',
                    'height' => '797',
                    'media' => '58512be6c3a5be49fdca1178',
                ],
                'baseImage' => [
                    'width' => '1400',
                    'height' => '929',
                    'media' => '58512be7c3a5be49fdca1182',
                ],
                '777x600' => [
                    'width' => '777',
                    'height' => '516',
                    'media' => '58512c44c3a5be49f3529d95',
                ],
            ],
        ];

        $mediaManager = $this->container->get('swp_content_bundle.manager.media');
        $fakeImage = __DIR__.'/../assets/test_cc_image.jpg';

        foreach ($articles as $articleData) {
            $article = $this->container->get('swp.factory.article')->create();
            $article->setTitle($articleData['title']);
            $article->setBody($articleData['content']);
            $article->setLocale($articleData['locale']);
            $article->setPublishedAt(new \DateTime());
            $article->setPublishable(true);
            $article->setCode(md5($articleData['title']));
            $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
            $manager->persist($article);

            foreach ($articleMedias as $articleMediaKey => $renditions) {
                // create Media
                $articleMediaClass = $this->container->getParameter('swp.model.media.class');
                $articleMedia = new $articleMediaClass();
                $articleMedia->setArticle($article);
                $articleMedia->setKey($articleMediaKey);
                $articleMedia->setBody('article media body');
                $articleMedia->setByLine('By Best Editor');
                $articleMedia->setLocated('Porto');
                $articleMedia->setDescription('Media description');
                $articleMedia->setUsageTerms('Some super open terms');
                $articleMedia->setMimetype('image/jpeg');
                $manager->persist($articleMedia);

                /* @var $rendition Rendition */
                foreach ($renditions as $key => $rendition) {
                    $uploadedFile = new UploadedFile($fakeImage, $rendition['media'], 'image/jpeg', filesize($fakeImage), null, true);
                    $image = $mediaManager->handleUploadedFile($uploadedFile, $rendition['media']);

                    if ('original' === $key) {
                        $articleMedia->setImage($image);
                    }

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

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 999;
    }
}
