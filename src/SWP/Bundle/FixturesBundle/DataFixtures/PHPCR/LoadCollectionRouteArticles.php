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
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

class LoadCollectionRouteArticles extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $manager;
    private $defaultTenantPrefix;

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

        $this->defaultTenantPrefix = $this->getTenantPrefix();

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
                    'parent' => $this->defaultTenantPrefix.'/routes',
                    'name' => 'collection-no-template',
                    'type' => 'collection',
                ],
                [
                    'parent' => $this->defaultTenantPrefix.'/routes',
                    'name' => 'collection-test',
                    'type' => 'collection',
                    'template_name' => 'collection.html.twig',
                ],
                [
                    'parent' => $this->defaultTenantPrefix.'/routes',
                    'name' => 'collection-content',
                    'type' => 'collection',
                    'articles_template_name' => 'test.html.twig',
                ],
            ],
        ];

        $routeService = $this->container->get('swp.service.route');

        foreach ($routes[$env] as $routeData) {
            $route = $routeService->createRoute($routeData);

            $manager->persist($route);
        }

        $manager->flush();
    }

    public function setRoutesContent($env, $manager)
    {
        $routes = [
            'test' => [
                [
                    'path' => $this->defaultTenantPrefix.'/routes/collection-content',
                    'content' => $this->defaultTenantPrefix.'/content/some-content',
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

    public function loadArticles($env, $manager)
    {
        $articles = [
            'test' => [
                [
                    'title' => 'Test art1',
                    'content' => 'Test art1 content',
                    'route' => $this->defaultTenantPrefix.'/routes/collection-test',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Test art2',
                    'content' => 'Test art2 content',
                    'route' => $this->defaultTenantPrefix.'/routes/collection-test',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Test art3',
                    'content' => 'Test art3',
                    'route' => $this->defaultTenantPrefix.'/routes/collection-test',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                ],
                [
                    'title' => 'Some content',
                    'content' => 'some content',
                    'route' => $this->defaultTenantPrefix.'/routes/collection-content',
                    'parent' => $this->defaultTenantPrefix.'/content',
                    'locale' => 'en',
                    'templateName' => 'some_content.html.twig',
                ],
                [
                    'title' => 'Some other content',
                    'content' => 'some other content',
                    'route' => $this->defaultTenantPrefix.'/routes/collection-content',
                    'parent' => $this->defaultTenantPrefix.'/content',
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
                if (isset($articleData['templateName'])) {
                    $article->setTemplateName($articleData['templateName']);
                }

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
        return 20;
    }
}
