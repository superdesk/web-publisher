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

namespace SWP\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\FixturesBundle\AbstractFixture;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;

class LoadArticlesData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();
        $this->loadRoutes($env, $manager);

        $this->loadFixtures(
            '@SWPFixturesBundle/Resources/fixtures/PHPCR/'.$env.'/article.yml',
            $manager
        );

        $this->setRoutesContent($env, $manager);
        $manager->flush();
    }

    public function loadRoutes($env, $manager)
    {
        $routes = [
            'dev' => [
                [
                    'parent' => '/swp/routes',
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z1-9\-_\/]+'
                    ],
                    'defaults' => [
                        '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderContainerPageAction'
                    ]
                ],
                [
                    'parent' => '/swp/routes',
                    'name' => 'articles',
                    'defaults' => [
                        '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderContentPageAction'
                    ]
                ],
                [
                    'parent' => '/swp/routes/articles',
                    'name' => 'features',
                    'defaults' => [
                        '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderContentPageAction'
                    ]
                ]
            ],
            'test' => [
                [
                    'parent' => '/swp/routes',
                    'name' => 'news',
                    'variablePattern' => '/{slug}',
                    'requirements' => [
                        'slug' => '[a-zA-Z1-9\-_\/]+'
                    ],
                    'defaults' => [
                        '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderContainerPageAction'
                    ]
                ],
                [
                    'parent' => '/swp/routes',
                    'name' => 'articles',
                    'defaults' => [
                        '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderContentPageAction'
                    ]
                ],
                [
                    'parent' => '/swp/routes/articles',
                    'name' => 'features',
                    'defaults' => [
                        '_controller' => '\SWP\WebRendererBundle\Controller\ContentController::renderContentPageAction'
                    ]
                ]
            ]
        ];

        foreach ($routes[$env] as $routeData) {
            $route = new Route();
            $route->setParentDocument($manager->find(null, $routeData['parent']));
            $route->setName($routeData['name']);

            if (array_key_exists('variablePattern', $routeData)) {
                $route->setVariablePattern($routeData['variablePattern']);
            }
            if (array_key_exists('requirements', $routeData)) {
                foreach ($routeData['requirements'] as $key => $value) {
                    $route->setRequirement($key, $value);
                }
            }

            if (array_key_exists('defaults', $routeData)) {
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
                    'path' => '/swp/routes/news',
                    'content' => '/swp/content/features',
                ],
                [
                    'path' => '/swp/routes/articles/features',
                    'content' => '/swp/content/features',
                ]
            ],
            'test' => [
                [
                    'path' => '/swp/routes/news',
                    'content' => '/swp/content/test-news-article',
                ],
                [
                    'path' => '/swp/routes/articles/features',
                    'content' => '/swp/content/features',
                ]
            ]
        ];

        foreach ($routes[$env] as $routeData) {
            if (array_key_exists('content', $routeData)) {
                $route = $manager->find(null, $routeData['path']);
                $route->setContent($manager->find(null, $routeData['content']));
            }
        }
    }
}
