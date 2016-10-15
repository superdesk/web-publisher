<?php

namespace SWP\Bundle\FixturesBundle\Registry;

class FixtureRegistry
{
    private $environment;

    public function getFixtures(array $fixtureNames)
    {
        // this array can be moved to config file
        $fixtures = [
            'doctrine_phpcr' => [
                'tenant' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
                'article' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
                'article_media' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesMediaData',
                'separate_article' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadSeparateArticlesData',
                'route' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData',
                'collection_route' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
                'homepage' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadHomepagesData',
                'menu' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenusData',
                'menu_node' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenuNodesData',
            ],
            'doctrine' => [
                'tenant' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadTenantsData',
                'article' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadArticlesData',
                'article_media' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadArticlesMediaData',
                'separate_article' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadSeparateArticlesData',
                'rule' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRulesData',
                'route' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRoutesData',
                'collection_route' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadCollectionRouteArticles',
                'menu' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadMenusData',
                'menu_node' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadMenuNodesData',
            ],
        ];

        $result = [];
        foreach ($fixtures[$this->environment] as $key => $fixture) {
            foreach ($fixtureNames as $fixtureName) {
                if ($key === $fixtureName) {
                    $result[] = $fixture;
                }
            }
        }

        return $result;
    }

    public function setEnvironment($env)
    {
        $this->environment = $env;
    }
}
