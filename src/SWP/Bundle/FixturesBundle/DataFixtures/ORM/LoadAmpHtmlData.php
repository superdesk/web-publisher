<?php

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadAmpHtmlData extends AbstractFixture implements FixtureInterface
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

            foreach ($articles as $articleData) {
                $article = $this->container->get('swp.factory.article')->create();
                $article->setTitle($articleData['title']);
                $article->setBody($articleData['content']);
                $article->setRoute($this->getRouteByName($articleData['route']));
                $article->setLocale($articleData['locale']);
                $article->setPublishable(true);
                $article->setPublishedAt(new \DateTime());
                $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
                $article->setTenantCode($articleData['tenant']);
                $manager->persist($article);

                $this->addReference($article->getSlug(), $article);
            }

            $manager->flush();
        }
    }
}
