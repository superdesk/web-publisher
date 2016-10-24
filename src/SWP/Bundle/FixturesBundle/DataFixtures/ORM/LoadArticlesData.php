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
use SWP\Bundle\ContentBundle\Doctrine\ORM\Article;
use SWP\Bundle\ContentBundle\Doctrine\ORM\Route;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

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

        $this->loadRoutes($env, $manager);
        $this->loadArticles($env, $manager);

        $manager->flush();
    }

    public function loadRoutes($env, $manager)
    {
        $routes = [
            'dev' => [
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
                    'templateName' => 'news.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                ],
                [
                    'name' => 'articles',
                    'type' => 'collection',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z1-9*\-_\/]+',
                    ],
                    'defaults' => [
                        'slug' => null,
                    ],
                ],
                [
                    'name' => 'get-involved',
                    'type' => 'content',
                ],
                [
                    'name' => 'features',
                    'type' => 'content',
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
            $route = new Route();
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
            $this->loadFixtures(
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/article.yml',
                $manager,
                [
                    'providers' => [$this],
                ]
            );
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
            foreach ($articles[$env] as $articleData) {
                $article = new Article();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setPublishable(true);
                $article->setPublishedAt(new \DateTime());
                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
                $manager->persist($article);

                $this->addReference($article->getSlug(), $article);
            }

            $manager->flush();
        }
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
