<?php

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

class LoadAmpHtmlData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
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
                    'name' => 'amp-articles',
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
                    'tenant' => '123abc',
                ],
                [
                    'name' => 'amp-articles-tenant-2',
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
                    'tenant' => '456def',
                ],
                [
                    'name' => 'some-content',
                    'type' => 'content',
                    'tenant' => '123abc',
                ],
            ];

            $routeService = $this->container->get('swp.service.route');
            $dispatcher = $this->container->get('event_dispatcher');

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
                    'title' => 'AMP Html Article',
                    'content' => 'Nihil repellat vero omnis voluptates id amet et. Suscipit qui recusandae totam nulla quam ipsam. Cupiditate sed natus debitis voluptas aut. Sit repudiandae esse perspiciatis dignissimos error. Itaque quibusdam tempora velit porro ut velit soluta. Eligendi occaecati debitis et saepe. Sint dolorem delectus enim ipsum inventore sed libero. Velit qui suscipit a deserunt laudantium quibusdam enim. Soluta qui ipsam non ipsum. Reiciendis aperiam et fuga doloribus nisi. <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fniemanlab%2Fposts%2F10154594541763654&width=500" width="500" height="482" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>',
                    'route' => 'amp-articles',
                    'locale' => 'en',
                    'tenant' => '123abc',
                ],
                [
                    'title' => 'AMP Html Article Tenant 2',
                    'content' => 'Nihil repellat vero omnis voluptates id amet et. Suscipit qui recusandae totam nulla quam ipsam. Cupiditate sed natus debitis voluptas aut. Sit repudiandae esse perspiciatis dignissimos error. Itaque quibusdam tempora velit porro ut velit soluta. Eligendi occaecati debitis et saepe. Sint dolorem delectus enim ipsum inventore sed libero. Velit qui suscipit a deserunt laudantium quibusdam enim. Soluta qui ipsam non ipsum. Reiciendis aperiam et fuga doloribus nisi. <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fniemanlab%2Fposts%2F10154594541763654&width=500" width="500" height="482" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>',
                    'route' => 'amp-articles-tenant-2',
                    'locale' => 'en',
                    'tenant' => '456def',
                ],
            ];

            $articleService = $this->container->get('swp.service.article');
            foreach ($articles as $articleData) {
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $dispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $dispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);
                $article->setLocale($articleData['locale']);
                $article->setCode(md5($articleData['title']));
                $package = $this->createPackage($articleData);
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

    public function getOrder()
    {
        return 4;
    }
}
