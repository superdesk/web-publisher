<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use SWP\Bundle\ContentBundle\Doctrine\ORM\RouteRepository;
use SWP\Bundle\ContentBundle\Model\Route;
use SWP\Bundle\ContentBundle\Provider\ORM\RouteProvider;
use Symfony\Cmf\Component\Routing\Candidates\Candidates;

class RouteProviderTest extends TestCase
{
    public function testGetWithChildrenByStaticPrefix()
    {
        $routeRepository = $this->getMockBuilder(RouteRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $newsRoute = new TestedRoute();
        $newsRoute->setStaticPrefix('/news');
        $newsRoute->setId(1);

        $sportRoute = new TestedRoute();
        $sportRoute->setStaticPrefix('/sport');
        $sportRoute->setId(2);

        $footballRoute = new TestedRoute();
        $footballRoute->setStaticPrefix('/sport/footbal');
        $footballRoute->setId(3);
        $footballRoute->setParent($sportRoute);

        $routeRepository->expects(self::any())
            ->method('findBy')
            ->withConsecutive([['staticPrefix' => ['/news']], []], [['staticPrefix' => ['/sport']], []])
            ->willReturnOnConsecutiveCalls([$newsRoute], [$sportRoute]);

        $managerRegistry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'getClassMetadata', 'persist', 'flush'])
            ->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($routeRepository));

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($em));

        $candidatesStrategy = $this->getMockBuilder(Candidates::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeProvider = $this->getMockBuilder(RouteProvider::class)
            ->setConstructorArgs([$routeRepository, $managerRegistry, $candidatesStrategy, Route::class])
            ->setMethods(['getChildrenByStaticPrefix'])
            ->getMock();

        $routeProvider->expects(self::any())
            ->method('getChildrenByStaticPrefix')
            ->withConsecutive([[2], []])
            ->willReturnOnConsecutiveCalls([$footballRoute]);

        self::assertEquals([1], $routeProvider->getWithChildrenByStaticPrefix(['/news']));
        self::assertEquals([2, 3], $routeProvider->getWithChildrenByStaticPrefix(['/sport/*']));
    }
}

class TestedRoute extends Route
{
    public function setId($id)
    {
        $this->id = $id;
    }
}
