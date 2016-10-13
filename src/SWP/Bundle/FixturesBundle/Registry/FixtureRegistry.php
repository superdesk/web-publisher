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
                'separate_article' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadSeparateArticlesData',
                'route' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData',
                'homepage' => 'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadHomepagesData',
            ],
            'doctrine' => [
                'tenant' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadTenantsData',
                'article' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadArticlesData',
                'separate_article' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadSeparateArticlesData',
                'rule' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRulesData',
                'route' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRoutesData',
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
