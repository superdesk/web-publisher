<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
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
use SWP\Bundle\ContentBundle\Model\Slideshow;
use Doctrine\Common\DataFixtures\AbstractFixture;
use SWP\Bundle\ContentBundle\Model\SlideshowItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadArticlesSlideshowsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $manager;

    private $container;

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
                'slideshows' => [
                    'slideshow1',
                    'slideshow3',
                ],
            ],
            [
                'title' => 'Test news article 2',
                'content' => 'Test news article content 2',
                'locale' => 'en',
                'slideshows' => [
                    'slideshow2',
                ],
            ],
        ];

        $renditions = [
            'original' => [
                'width' => '4000',
                'height' => '2667',
                'media' => '12345678987654321a',
            ],
            '16-9' => [
                'width' => '1079',
                'height' => '720',
                'media' => '12345678987654321b',
            ],
            '4-3' => [
                'width' => '800',
                'height' => '533',
                'media' => '12345678987654321c',
            ],
        ];

        $mediaManager = $this->container->get('swp_content_bundle.manager.media');
        $fakeImage = __DIR__.'/../../Resources/assets/test_cc_image.jpg';

        foreach ($articles as $articleData) {
            /** @var ArticleInterface $article */
            $article = $this->container->get('swp.factory.article')->create();
            $article->setTitle($articleData['title']);
            $article->setBody($articleData['content']);
            $article->setLocale($articleData['locale']);
            $article->setPublishedAt(new \DateTime());
            $article->setPublishable(true);
            $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
            $article->setCode(md5($articleData['title']));
            $manager->persist($article);

            // create Media
            $articleMediaClass = $this->container->getParameter('swp.model.media.class');
            $articleMedia = new $articleMediaClass();
            $articleMedia->setArticle($article);
            $articleMedia->setKey('embedded6358005131');
            $articleMedia->setBody('article media body');
            $articleMedia->setByLine('By Best Editor');
            $articleMedia->setLocated('Porto');
            $articleMedia->setDescription('Media description');
            $articleMedia->setUsageTerms('Some super open terms');
            $articleMedia->setMimetype('image/jpeg');
            $manager->persist($articleMedia);

            /* @var $rendition Rendition */
            foreach ($renditions as $key => $rendition) {
                $uploadedFile = new UploadedFile($fakeImage, $rendition['media'], 'image/jpeg', null, true);
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

            if (isset($articleData['slideshows'])) {
                foreach ((array) $articleData['slideshows'] as $slideshowCode) {
                    $slideshow = new Slideshow();
                    $slideshow->setCode($slideshowCode);
                    $slideshow->setArticle($article);
                    $slideshowItem = new SlideshowItem();
                    $slideshowItem->setArticleMedia($articleMedia);
                    $slideshowItem->setSlideshow($slideshow);
                    $manager->persist($slideshowItem);
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
