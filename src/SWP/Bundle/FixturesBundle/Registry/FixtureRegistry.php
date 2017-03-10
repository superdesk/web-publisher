<?php

namespace SWP\Bundle\FixturesBundle\Registry;

class FixtureRegistry
{
    private $environment;

    public function getFixtures(array $fixtureNames)
    {
        // this array can be moved to config file
        $fixtures = [
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
                'user' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadUsersData',
                'container' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadContainersData',
                'container_widget' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadContainerWidgetsData',
                'amp_html' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadAmpHtmlData',
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
        $result[] = 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadUsersData';

        return $result;
    }

    public function setEnvironment($env)
    {
        $this->environment = $env;
    }
}
