<?php

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\ContentBundle\Model\RouteToArticle;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRouteToArticlesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const TEST_PRIORITY = 1;
    const TEST_RULE = 'article.getLocale() matches "/en/"';
    const TEST_ROUTE_ID = '';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->container->get('swp.repository.tenant')->findOneBySubdomain('default');

        $routeToArticle = new RouteToArticle();
        $routeToArticle->setTenantCode($tenant->getCode());
        $routeToArticle->setPriority(self::TEST_PRIORITY);
        $routeToArticle->setRouteId(self::TEST_ROUTE_ID);
        $routeToArticle->setRule(self::TEST_RULE);

        $manager->persist($routeToArticle);
        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
