<?php

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadArticlesWithMetadata extends AbstractFixture implements FixtureInterface
{
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $env = $this->getEnvironment();

        if ($env === 'test') {
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
                    'templateName' => 'articles_by_metadata_v1.html.twig',
                    'articlesTemplateName' => 'article.html.twig',
                    'tenant' => '123abc',
                ],
            ];

            $routeService = $this->container->get('swp.service.route');

            foreach ($routes as $routeData) {
                $route = $this->container->get('swp.factory.route')->create();
                $route->setName($routeData['name']);
                $route->setType($routeData['type']);

                if (isset($routeData['templateName'])) {
                    $route->setTemplateName($routeData['templateName']);
                }

                if (isset($routeData['articlesTemplateName'])) {
                    $route->setArticlesTemplateName($routeData['articlesTemplateName']);
                }

                if (isset($routeData['tenant'])) {
                    $route->setTenantCode($routeData['tenant']);
                }

                $route = $routeService->fillRoute($route);

                $manager->persist($route);
            }

            $manager->flush();

            $articles = [
                [
                    'title' => 'Article 1',
                    'content' => 'test content 1',
                    'route' => 'news',
                    'locale' => 'en',
                    'tenant' => '123abc',
                    'author' => 'Test Persona',
                ],
                [
                    'title' => 'Article 2',
                    'content' => 'test content 2',
                    'route' => 'news',
                    'locale' => 'en',
                    'tenant' => '123abc',
                    'author' => 'Karen Ruhiger',
                ],
            ];

            $articleService = $this->container->get('swp.service.article');
            foreach ($articles as $articleData) {
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                $article->setMetadata([
                    'located' => 'Sydney',
                    'byline' => $articleData['author'],
                ]);
                $manager->persist($article);
                $articleService->publish($article);
                $article->setTenantCode($articleData['tenant']);

                $this->addReference($article->getSlug(), $article);
            }

            $manager->flush();
        }
    }
}
