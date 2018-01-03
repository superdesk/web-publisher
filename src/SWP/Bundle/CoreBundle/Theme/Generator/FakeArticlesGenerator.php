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

use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;
use SWP\Bundle\ContentBundle\Service\ArticleSourceServiceInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Faker;

class FakeArticlesGenerator implements FakeArticlesGeneratorInterface
{
    /**
     * @var ArticleFactoryInterface
     */
    protected $articleFactory;

    /**
     * @var ArticleServiceInterface
     */
    protected $articleService;

    /**
     * @var FactoryInterface
     */
    protected $articleSourceFactory;

    /**
     * @var ArticleSourceServiceInterface
     */
    protected $articleSourceService;

    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * FakeArticlesGenerator constructor.
     *
     * @param ArticleFactoryInterface       $articleFactory
     * @param ArticleServiceInterface       $articleService
     * @param FactoryInterface              $articleSourceFactory
     * @param ArticleSourceServiceInterface $articleSourceService
     * @param ArticleRepositoryInterface    $articleRepository
     */
    public function __construct(ArticleFactoryInterface $articleFactory, ArticleServiceInterface $articleService, FactoryInterface $articleSourceFactory, ArticleSourceServiceInterface $articleSourceService, ArticleRepositoryInterface $articleRepository)
    {
        $this->articleFactory = $articleFactory;
        $this->articleService = $articleService;
        $this->articleSourceFactory = $articleSourceFactory;
        $this->articleSourceService = $articleSourceService;
        $this->articleRepository = $articleRepository;
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
            $article->setTitle($faker->catchPhrase());
            $article->setBody($faker->paragraph(20));
            $article->setLead($faker->paragraph(3));
            $article->setLocale('en');
            $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
            $article->setPublishedAt(new \DateTime());
            $article->setPublishable(true);
            $article->setCode($faker->uuid);
            $this->articleRepository->persist($article);
            $this->createArticleMedia($article);

            $articles[] = $article;
        }

        return $articles;
    }

    protected function createArticleMedia(ArticleInterface $article)
    {
//        $image = $mediaManager->handleUploadedFile($uploadedFile, $mediaId);
//        $articleMedia->setImage($image);
//
//        $imageRendition = new ImageRendition();
//        $imageRendition->setImage($image);
//        $imageRendition->setHeight($rendition['height']);
//        $imageRendition->setWidth($rendition['width']);
//        $imageRendition->setName($key);
//        $imageRendition->setMedia($articleMedia);
//        $articleMedia->addRendition($imageRendition);
//        $manager->persist($imageRendition);
//    }
}
