<?php

declare(strict_types=1);

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadPackagesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();

        if ('test' === $env) {
            $this->loadFixtures(
                [
                    '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/package.yml',
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 6;
    }
}
