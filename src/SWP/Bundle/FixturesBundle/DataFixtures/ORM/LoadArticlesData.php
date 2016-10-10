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
    private $defaultTenantPrefix;
    private $firstTenantPrefix;

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
                        'slug' => '[a-zA-Z1-9\-_\/]+',
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
                        'slug' => '[a-zA-Z1-9\-_\/]+',
                    ],
                    'type' => 'collection',
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
                        'slug' => '[a-zA-Z0-9\-_\/]+',
                    ],
                    'type' => 'collection',
                    'defaults' => [
                        'slug' => null,
                    ],
                ],
                [
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z0-9\-_\/]+',
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
                    'name' => 'features',
                    'type' => 'content',
                ],
                [
                    'name' => 'features',
                    'type' => 'content',
                ],
            ],
        ];

        foreach ($routes[$env] as $routeData) {
            $route = new Route();
            $route->setName('swp_'.$routeData['name']);
            $route->setStaticPrefix('/'.$routeData['name']);
            $route->setType($routeData['type']);

            if (isset($routeData['cacheTimeInSeconds'])) {
                $route->setCacheTimeInSeconds($routeData['cacheTimeInSeconds']);
            }

            if (isset($routeData['variablePattern'])) {
                $route->setVariablePattern($routeData['variablePattern']);
            }

            if (isset($routeData['requirements'])) {
                foreach ($routeData['requirements'] as $key => $value) {
                    $route->setRequirement($key, $value);
                }
            }

            if (isset($routeData['templateName'])) {
                $route->setTemplateName($routeData['templateName']);
            }

            if (isset($routeData['articlesTemplateName'])) {
                $route->setArticlesTemplateName($routeData['articlesTemplateName']);
            }

            if (isset($routeData['defaults'])) {
                foreach ($routeData['defaults'] as $key => $value) {
                    $route->setDefault($key, $value);
                }
            }

            $manager->persist($route);
        }

        $manager->flush();
    }

//    public function setRoutesContent($env, $manager)
//    {
//        $routes = [
//            'dev' => [
//                [
//                    'path' => $this->defaultTenantPrefix.'/routes/articles/features',
//                    'content' => $this->defaultTenantPrefix.'/content/features',
//                ],
//                [
//                    'path' => $this->defaultTenantPrefix.'/routes/articles/get-involved',
//                    'content' => $this->defaultTenantPrefix.'/content/get-involved',
//                ],
//            ],
//            'test' => [
//                [
//                    'path' => $this->defaultTenantPrefix.'/routes/news',
//                    'content' => $this->defaultTenantPrefix.'/content/test-news-article',
//                ],
//                [
//                    'path' => $this->defaultTenantPrefix.'/routes/articles/features',
//                    'content' => $this->defaultTenantPrefix.'/content/features',
//                ],
//                [
//                    'path' => $this->firstTenantPrefix.'/routes/features',
//                    'content' => $this->firstTenantPrefix.'/content/features-client1',
//                ],
//            ],
//        ];

//        foreach ($routes[$env] as $routeData) {
//            if (array_key_exists('content', $routeData)) {
//                $route = $manager->find(null, $routeData['path']);
//                $route->setContent($manager->find(null, $routeData['content']));
//            }
//        }
//    }

    /**
     * Sets articles manually (not via Alice) for test env due to fatal error:
     * Method PHPCRProxies\__CG__\Doctrine\ODM\PHPCR\Document\Generic::__toString() must not throw an exception.
     */
    public function loadArticles($env, $manager)
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
                    'route' => $this->defaultTenantPrefix.'/routes/news',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Test article',
                    'content' => 'Test article content',
                    'route' => $this->defaultTenantPrefix.'/routes/news',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Features',
                    'content' => 'Features content',
                    'route' => $this->defaultTenantPrefix.'/routes/news',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Features client1',
                    'content' => 'Features client1 content',
                    'route' => $this->firstTenantPrefix.'/routes/news',
                    'parent' => $this->firstTenantPrefix.'/content',
                    'locale' => 'en',
                ],
            ],
        ];

        if (isset($articles[$env])) {
            foreach ($articles[$env] as $articleData) {
                $article = new Article();
                $article->setParentDocument($manager->find(null, $articleData['parent']));
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($manager->find(null, $articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setPublishable(true);
                $article->setPublishedAt(new \DateTime());
                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
                $manager->persist($article);
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
