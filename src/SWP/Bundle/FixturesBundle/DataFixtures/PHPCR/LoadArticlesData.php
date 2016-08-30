<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
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

        $this->defaultTenantPrefix = $this->getTenantPrefix();
        $this->firstTenantPrefix = $this->getTenantPrefix('client1');

        $this->loadRoutes($env, $manager);
        $this->loadArticles($env, $manager);
        $this->setRoutesContent($env, $manager);

        $manager->flush();
    }

    public function loadRoutes($env, $manager)
    {
        $routes = [
            'dev' => [
                [
                    'parent' => $this->defaultTenantPrefix.'/routes',
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
                ],
                [
                    'parent' => $this->defaultTenantPrefix.'/routes',
                    'name' => 'articles',
                    'type' => 'content',
                ],
                [
                    'parent' => $this->defaultTenantPrefix.'/routes/articles',
                    'name' => 'get-involved',
                    'type' => 'content',
                ],
                [
                    'parent' => $this->defaultTenantPrefix.'/routes/articles',
                    'name' => 'features',
                    'type' => 'content',
                ],
            ],
            'test' => [
                [
                    'parent' => $this->defaultTenantPrefix.'/routes',
                    'name' => 'news',
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
                    'parent' => $this->firstTenantPrefix.'/routes',
                    'name' => 'news',
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
                    'parent' => $this->defaultTenantPrefix.'/routes',
                    'name' => 'articles',
                    'type' => 'content',
                ],
                [
                    'parent' => $this->defaultTenantPrefix.'/routes/articles',
                    'name' => 'features',
                    'type' => 'content',
                ],
                [
                    'parent' => $this->firstTenantPrefix.'/routes',
                    'name' => 'features',
                    'type' => 'content',
                ],
            ],
        ];

        foreach ($routes[$env] as $routeData) {
            $route = new Route();
            $route->setParentDocument($manager->find(null, $routeData['parent']));
            $route->setName($routeData['name']);
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

            if (isset($routeData['defaults'])) {
                foreach ($routeData['defaults'] as $key => $value) {
                    $route->setDefault($key, $value);
                }
            }
            $manager->persist($route);
        }

        $manager->flush();
    }

    public function setRoutesContent($env, $manager)
    {
        $routes = [
            'dev' => [
                [
                    'path' => $this->defaultTenantPrefix.'/routes/articles/features',
                    'content' => $this->defaultTenantPrefix.'/content/features',
                ],
                [
                    'path' => $this->defaultTenantPrefix.'/routes/articles/get-involved',
                    'content' => $this->defaultTenantPrefix.'/content/get-involved',
                ],
            ],
            'test' => [
                [
                    'path' => $this->defaultTenantPrefix.'/routes/news',
                    'content' => $this->defaultTenantPrefix.'/content/test-news-article',
                ],
                [
                    'path' => $this->defaultTenantPrefix.'/routes/articles/features',
                    'content' => $this->defaultTenantPrefix.'/content/features',
                ],
                [
                    'path' => $this->firstTenantPrefix.'/routes/features',
                    'content' => $this->firstTenantPrefix.'/content/features-client1',
                ],
            ],
        ];

        foreach ($routes[$env] as $routeData) {
            if (array_key_exists('content', $routeData)) {
                $route = $manager->find(null, $routeData['path']);
                $route->setContent($manager->find(null, $routeData['content']));
            }
        }
    }

    /**
     * Sets articles manually (not via Alice) for test env due to fatal error:
     * Method PHPCRProxies\__CG__\Doctrine\ODM\PHPCR\Document\Generic::__toString() must not throw an exception.
     */
    public function loadArticles($env, $manager)
    {
        if ($env !== 'test') {
            $this->loadFixtures(
                '@SWPFixturesBundle/Resources/fixtures/PHPCR/'.$env.'/article.yml',
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
                $article->setPublishedAt(new \DateTime());
                $article->setPublishable(true);
                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);

                $manager->persist($article);
            }

            $manager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
