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
            ],
            'doctrine' => [
                'tenant' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadTenantsData',
                'article' => 'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadArticlesData',
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
