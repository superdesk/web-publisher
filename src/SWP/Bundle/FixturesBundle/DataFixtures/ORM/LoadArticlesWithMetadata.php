<?php

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Component\Bridge\Model\ExternalDataInterface;

class LoadArticlesWithMetadata extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $env = $this->getEnvironment();

        if ('test' === $env) {
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
            $metadataFactory = $this->container->get('swp.factory.metadata');
            foreach ($articles as $articleData) {
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                $legacyMetadata = [
                    'located' => 'Sydney',
                    'byline' => $articleData['author'],
                ];

                $article->setMetadata($legacyMetadata);
                $metadata = $metadataFactory->createFrom($legacyMetadata);
                $article->setData($metadata);
                $package = $this->createPackage($articleData);
                /** @var ExternalDataInterface $firstExternalData */
                $firstExternalData = $this->container->get('swp.factory.external_data')->create();
                $firstExternalData->setKey('some test data');
                $firstExternalData->setValue('SOME TEST VALUE');
                $firstExternalData->setPackage($package);
                $secondExternalData = $this->container->get('swp.factory.external_data')->create();
                $secondExternalData->setKey(34);
                $secondExternalData->setValue('another value');
                $secondExternalData->setPackage($package);
                $manager->persist($firstExternalData);
                $manager->persist($secondExternalData);
                $manager->persist($package);
                $article->setPackage($package);

                $manager->persist($article);
                $articleService->publish($article);
                $article->setTenantCode($articleData['tenant']);

                $this->addReference($article->getSlug(), $article);
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

    public function getOrder(): int
    {
        return 20;
    }
}
