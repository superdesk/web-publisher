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
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
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
        ];

        $routeService = $this->container->get('swp.service.route');

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

            $route = $routeService->fillRoute($route);

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
                'route' => 'news',
                'locale' => 'en',
                'status' => ArticleInterface::STATUS_CANCELED,
            ],
        ];

        $routeProvider = $this->container->get('swp.provider.route');
        foreach ($articles as $articleData) {
            /** @var ArticleInterface $article */
            $article = $this->container->get('swp.factory.article')->create();
            $article->setTitle($articleData['title']);
            $article->setBody($articleData['content']);
            $article->setRoute($routeProvider->getRouteByName($articleData['route']));
            $article->setLocale($articleData['locale']);
            if (!isset($articleData['status'])) {
                $article->setPublishable(true);
                $article->setPublishedAt(new \DateTime());
                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
            } else {
                $article->setStatus($articleData['status']);
            }

            $article->setMetadata($this->articleMetadata());
            $article->setCode(md5($articleData['title']));
            $manager->persist($article);

            $this->addReference($article->getSlug(), $article);
        }

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
