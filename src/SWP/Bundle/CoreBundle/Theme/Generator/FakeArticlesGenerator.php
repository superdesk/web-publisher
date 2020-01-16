<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Faker;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleMediaInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\CoreBundle\Model\Image;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FakeArticlesGenerator implements FakeArticlesGeneratorInterface
{
    /**
     * @var ArticleFactoryInterface
     */
    protected $articleFactory;

    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * @var MediaFactoryInterface
     */
    protected $articleMediaFactory;

    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * @var FactoryInterface
     */
    protected $articleStatisticsFactory;

    /**
     * @var FactoryInterface
     */
    protected $packageFactory;

    /**
     * @var FactoryInterface
     */
    protected $itemFactory;

    /**
     * FakeArticlesGenerator constructor.
     */
    public function __construct(
        ArticleFactoryInterface $articleFactory,
        MediaManagerInterface $mediaManager,
        MediaFactoryInterface $articleMediaFactory,
        ArticleRepositoryInterface $articleRepository,
        FactoryInterface $articleStatisticsFactory,
        FactoryInterface $packageFactory,
        FactoryInterface $itemFactory
    ) {
        $this->articleFactory = $articleFactory;
        $this->mediaManager = $mediaManager;
        $this->articleMediaFactory = $articleMediaFactory;
        $this->articleRepository = $articleRepository;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->packageFactory = $packageFactory;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(int $numberOfArticles): array
    {
        $articles = [];
        for (; $numberOfArticles > 0; --$numberOfArticles) {
            /** @var ArticleInterface $article */
            $article = $this->articleFactory->create();
            $faker = Faker\Factory::create();
            $code = $faker->uuid;
            $title = $faker->catchPhrase();
            $body = $faker->paragraph(20);
            $article->setTitle($title);
            $article->setBody($body);
            $article->setLead($faker->paragraph(3));
            $article->setLocale('en');
            $article->setMetadata(['located' => 'Porto']);
            $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
            $article->setPublishedAt(new \DateTime());
            $article->setPublishable(true);
            $article->setCode($code);
            $this->articleRepository->persist($article);
            $this->articleRepository->flush();
            $article->setMedia($this->createArticleMedia($article));
            $article->setArticleStatistics($this->createArticleStatistics($article));
            $author = new ArticleAuthor();
            $author->setRole('editor');
            $author->setName('John Doe');
            $article->setAuthors(new ArrayCollection([$author]));

            /** @var PackageInterface $package */
            $package = $this->packageFactory->create();
            $package->setStatus(PackageInterface::STATUS_PUBLISHED);
            $package->setType('composite');
            $package->setGuid($code);
            $package->setLocated('Porto');
            $package->setHeadline($title);
            $package->setLanguage('en');
            $package->setPriority(1);
            $package->setVersion(1);

            /** @var ItemInterface $item */
            $item = $this->itemFactory->create();
            $item->setPubStatus(ContentInterface::STATUS_USABLE);
            $item->setType('text');
            $item->setGuid($code);
            $item->setLocated('Porto');
            $item->setHeadline($title);
            $item->setLanguage('en');
            $item->setPriority(1);
            $item->setVersion(1);
            $item->setBody($body);
            $item->setAuthors(new ArrayCollection([$author]));
            $package->setItems(new ArrayCollection([$item]));
            $this->articleRepository->persist($package);
            $article->setPackage($package);

            $this->articleRepository->flush();

            $articles[] = $article;
        }

        return $articles;
    }

    protected function createArticleStatistics(ArticleInterface $article): ArticleStatisticsInterface
    {
        /** @var ArticleStatisticsInterface $articleStatistics */
        $articleStatistics = $this->articleStatisticsFactory->create();
        $articleStatistics->setArticle($article);
        $articleStatistics->setPageViewsNumber(0);
        $this->articleRepository->persist($articleStatistics);

        return $articleStatistics;
    }

    protected function createArticleMedia(ArticleInterface $article): Collection
    {
        $mediaId = \uniqid('', false);

        $im = imagecreatetruecolor(800, 800);
        $textColor = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5, $article->getTitle(), $textColor);
        $fakeImage = sys_get_temp_dir().'/'.$article->getSlug().'.jpg';
        imagejpeg($im, $fakeImage);
        imagedestroy($im);

        $uploadedFile = new UploadedFile($fakeImage, $mediaId, 'image/jpeg', filesize($fakeImage), null, true);
        /** @var Image $image */
        $image = $this->mediaManager->handleUploadedFile($uploadedFile, $mediaId);
        /** @var ArticleMediaInterface $articleMedia */
        $articleMedia = $this->articleMediaFactory->createEmpty();
        $articleMedia->setImage($image);
        $articleMedia->setArticle($article);
        $articleMedia->setKey('embedded'.uniqid());
        $articleMedia->setBody('This is very nice image caption...');
        $articleMedia->setByLine('By Best Editor');
        $articleMedia->setLocated('Porto');
        $articleMedia->setDescription('Media description');
        $articleMedia->setUsageTerms('Some super open terms');
        $articleMedia->setMimetype('image/jpeg');
        $article->setFeatureMedia($articleMedia);
        $this->articleRepository->persist($articleMedia);

        $imageRendition = new ImageRendition();
        $imageRendition->setImage($image);
        $imageRendition->setHeight(800);
        $imageRendition->setWidth(800);
        $imageRendition->setName('original');
        $imageRendition->setMedia($articleMedia);
        $articleMedia->addRendition($imageRendition);
        $this->articleRepository->persist($imageRendition);

        return new ArrayCollection([$articleMedia]);
    }
}
