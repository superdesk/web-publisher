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
use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Component\Common\Criteria\Criteria;

class LoadCollectionRouteArticles extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $env = $this->getEnvironment();

        if ('test' !== $env) {
            return;
        }

        $this->loadRoutes($env, $manager);
        $this->loadArticles($env, $manager);
        $this->setRoutesContent($env, $manager);

        $manager->flush();
    }

    public function loadRoutes($env, ObjectManager $manager)
    {
        $routes = [
            'test' => [
                [
                    'name' => 'collection-no-template',
                    'type' => 'collection',
                ],
                [
                    'name' => 'collection-test',
                    'type' => 'collection',
                    'template_name' => 'collection.html.twig',
                ],
                [
                    'name' => 'collection-content',
                    'type' => 'collection',
                    'articles_template_name' => 'test.html.twig',
                ],
                [
                    'name' => 'collection-with-content',
                    'type' => 'collection',
                    'articles_template_name' => 'test.html.twig',
                ],
            ],
        ];

        $routeService = $this->container->get('swp.service.route');

        foreach ($routes[$env] as $routeData) {
            $route = $this->container->get('swp.factory.route')->create();
            $route->setName($routeData['name']);
            $route->setType($routeData['type']);

            if (isset($routeData['template_name'])) {
                $route->setTemplateName($routeData['template_name']);
            }

            if (isset($routeData['articles_template_name'])) {
                $route->setArticlesTemplateName($routeData['articles_template_name']);
            }

            $routeService->createRoute($route);

            $manager->persist($route);
        }

        $manager->flush();
    }

    public function setRoutesContent($env, $manager)
    {
        $routes = [
            'test' => [
                [
                    'name' => 'collection-with-content',
                    'content' => 'content-assigned-as-route-content',
                ],
            ],
        ];

        foreach ($routes[$env] as $routeData) {
            if (array_key_exists('content', $routeData)) {
                $articleProvider = $this->container->get('swp.provider.article');
                $routeProvider = $this->container->get('swp.provider.route');
                $route = $routeProvider->getRouteByName($routeData['name']);
                $criteria = new Criteria();
                $criteria->set('slug', $routeData['content']);
                $route->setContent($articleProvider->getOneByCriteria($criteria));
            }
        }
    }

    public function loadArticles($env, $manager)
    {
        $articles = [
            'test' => [
                [
                    'title' => 'Test art1',
                    'content' => 'Test art1 content',
                    'route' => 'collection-test',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Test art2',
                    'content' => 'Test art2 content',
                    'route' => 'collection-test',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Test art3',
                    'content' => 'Test art3',
                    'route' => 'collection-test',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Some content',
                    'content' => 'some content',
                    'route' => 'collection-content',
                    'locale' => 'en',
                    'templateName' => 'some_content.html.twig',
                ],
                [
                    'title' => 'Some other content',
                    'content' => 'some other content',
                    'route' => 'collection-content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Content assigned as route content',
                    'content' => 'some other content assigned as route content',
                    'locale' => 'en',
                ],
            ],
        ];

        if (isset($articles[$env])) {
            $routeProvider = $this->container->get('swp.provider.route');
            $articleService = $this->container->get('swp.service.article');
            foreach ($articles[$env] as $articleData) {
                /** @var ArticleInterface $article */
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                if (isset($articleData['templateName'])) {
                    $article->setTemplateName($articleData['templateName']);
                }
                if (isset($articleData['route'])) {
                    $article->setRoute($routeProvider->getRouteByName($articleData['route']));
                }

                $package = $this->createPackage($articleData);
                $manager->persist($package);
                $article->setPackage($package);

                $manager->persist($article);
                $articleService->publish($article);
            }

            $manager->flush();
        }
    }

    private function createPackage(array $articleData)
    {
        /** @var PackageInterface $package */
        $package = $this->container->get('swp.factory.package')->create();
        $package->setHeadline($articleData['title']);
        $package->setType('text');
        $package->setPubStatus('usable');
        $package->setGuid($this->container->get('swp_multi_tenancy.random_string_generator')->generate(10));
        $package->setLanguage('en');
        $package->setUrgency(1);
        $package->setPriority(1);
        $package->setVersion(1);

        return $package;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }
}
