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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\Metadata;
use SWP\Bundle\ContentBundle\Model\RelatedArticle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadArticlesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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

        $this->loadRoutes($manager);
        $this->loadArticles($manager);
    }

    public function loadRoutes($manager)
    {
        $routes = [
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
                'name' => 'lifestyle',
                'type' => 'collection',
                'parent' => 'articles',
            ],
        ];

        $routeService = $this->container->get('swp.service.route');

        $routesCache = [];
        foreach ($routes as $routeData) {
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

            if (isset($routeData['parent'])) {
                $route->setParent($routesCache[$routeData['parent']]);
            }

            $route = $routeService->fillRoute($route);

            $routesCache[$routeData['name']] = $route;
            $manager->persist($route);
        }

        $manager->flush();
    }

    public function loadArticles(ObjectManager $manager)
    {
        $articles = [
            [
                'title' => 'Test news article',
                'content' => 'Test news article content',
                'route' => 'news',
                'locale' => 'en',
                'sources' => ['aap'],
            ],
            [
                'title' => 'Test article',
                'content' => 'Test article content',
                'route' => 'news',
                'locale' => 'en',
                'published_at' => '2017-04-05 12:12:00',
                'extra' => [
                    'video' => 'YES',
                    'rafal-embed' => [
                        'embed' => 'embed link',
                        'description' => "Shakin' Stevens"
                    ]
                ],
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
            [
                'title' => 'Article 1',
                'content' => 'article 1 content',
                'route' => 'news',
                'locale' => 'en',
                'status' => ArticleInterface::STATUS_NEW,
            ],
            [
                'title' => 'Article 2',
                'content' => 'article 2 content',
                'route' => 'news',
                'locale' => 'en',
                'status' => ArticleInterface::STATUS_UNPUBLISHED,
            ],
            [
                'title' => 'Article 3',
                'content' => 'article 3 content',
                'route' => 'articles',
                'locale' => 'en',
                'status' => ArticleInterface::STATUS_CANCELED,
            ],
            [
                'title' => 'Lifestyle article 1',
                'content' => 'Lifestyle article content',
                'route' => 'lifestyle',
                'locale' => 'en',
            ],
        ];

        $routeProvider = $this->container->get('swp.provider.route');
        $keywordFactory = $this->container->get('swp.factory.keyword');
        $metadataFactory = $this->container->get('swp.factory.metadata');
        foreach ($articles as $articleData) {
            /** @var ArticleInterface $article */
            $article = $this->container->get('swp.factory.article')->create();
            $article->setTitle($articleData['title']);
            $article->setBody($articleData['content']);
            $article->setRoute($routeProvider->getRouteByName($articleData['route']));
            $article->setLocale($articleData['locale']);

            $author = new ArticleAuthor();
            $author->setRole('Writer');
            $author->setName('John Doe');
            $article->addAuthor($author);

            $keyword1 = $keywordFactory->create('Big city');
            $keyword2 = $keywordFactory->create('traffic');

            $article->addKeyword($keyword1);
            $article->addKeyword($keyword2);

            if (!isset($articleData['status'])) {
                $article->setPublishable(true);
                if (isset($articleData['published_at'])) {
                    $article->setPublishedAt(new \DateTime($articleData['published_at']));
                } else {
                    $article->setPublishedAt(new \DateTime());
                }

                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
            } else {
                $article->setStatus($articleData['status']);
            }

            if (isset($articleData['extra'])) {
                $article->setExtra($articleData['extra']);
                $article->setExtraFields($articleData['extra']);
            }

            $metadata = $this->articleMetadata();
            $article->setData($metadataFactory->createFrom($metadata));
            $article->setMetadata($metadata);
            $article->setCode(md5($articleData['title']));
            if (array_key_exists('sources', $articleData)) {
                foreach ($articleData['sources'] as $source) {
                    $this->container->get('swp.adder.article_source')->add($article, $source);
                }
            }

            $manager->persist($article);

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

        $manager->flush();
    }

    /**
     * Article example metadata.
     *
     * @return array
     */
    public function articleMetadata()
    {
        return [
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
