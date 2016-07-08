<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Document\Article;
use SWP\Bundle\ContentBundle\Document\Route;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadArticlesData extends AbstractFixture implements FixtureInterface
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
        $this->setRoutesContent($env, $manager);

        $manager->flush();
    }

    public function loadRoutes($env, $manager)
    {
        $routes = [
            'dev' => [
                [
                    'parent' => '/swp/default/routes',
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z1-9\-_\/]+',
                    ],
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/default/routes',
                    'name' => 'articles',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/default/routes/articles',
                    'name' => 'get-involved',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/default/routes/articles',
                    'name' => 'features',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction',
                    ],
                ],
            ],
            'test' => [
                [
                    'parent' => '/swp/default/routes',
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z1-9\-_\/]+',
                    ],
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/client1/routes',
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z1-9\-_\/]+',
                    ],
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/default/routes',
                    'name' => 'articles',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/default/routes/articles',
                    'name' => 'features',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/client1/routes',
                    'name' => 'features',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/default/routes',
                    'name' => 'homepage',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction',
                    ],
                ],
                [
                    'parent' => '/swp/client1/routes',
                    'name' => 'homepage',
                    'defaults' => [
                        '_controller' => '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction',
                    ],
                ],
            ],
        ];

        foreach ($routes[$env] as $routeData) {
            $route = new Route();
            $route->setParentDocument($manager->find(null, $routeData['parent']));
            $route->setName($routeData['name']);

            if (isset($routeData['variablePattern'])) {
                $route->setVariablePattern($routeData['variablePattern']);
            }

            if (isset($routeData['requirements'])) {
                foreach ($routeData['requirements'] as $key => $value) {
                    $route->setRequirement($key, $value);
                }
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
                    'path' => '/swp/default/routes/news',
                    'content' => '/swp/default/content/features',
                ],
                [
                    'path' => '/swp/default/routes/articles/features',
                    'content' => '/swp/default/content/features',
                ],
                [
                    'path' => '/swp/default/routes/articles/get-involved',
                    'content' => '/swp/default/content/get-involved',
                ],
            ],
            'test' => [
                [
                    'path' => '/swp/default/routes/news',
                    'content' => '/swp/default/content/test-news-article',
                ],
                [
                    'path' => '/swp/default/routes/articles/features',
                    'content' => '/swp/default/content/features',
                ],
                [
                    'path' => '/swp/client1/routes/features',
                    'content' => '/swp/client1/content/features-client1',
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
                    'route' => '/swp/default/routes/news',
                    'parent' => '/swp/default/content',
                ],
                [
                    'title' => 'Test article',
                    'content' => 'Test article content',
                    'route' => '/swp/default/routes/news',
                    'parent' => '/swp/default/content',
                ],
                [
                    'title' => 'Features',
                    'content' => 'Features content',
                    'route' => '/swp/default/routes/news',
                    'parent' => '/swp/default/content',
                ],
                [
                    'title' => 'Features client1',
                    'content' => 'Features client1 content',
                    'route' => '/swp/client1/routes/news',
                    'parent' => '/swp/client1/content',
                ],
            ],
        ];

        if (isset($articles[$env])) {
            foreach ($articles[$env] as $articleData) {
                $article = new Article();
                $article->setParent($manager->find(null, $articleData['parent']));
                $article->setTitle($articleData['title']);
                $article->setContent($articleData['content']);
                $article->setRoute($manager->find(null, $articleData['route']));

                $manager->persist($article);
            }

            $manager->flush();
        }
    }
}
