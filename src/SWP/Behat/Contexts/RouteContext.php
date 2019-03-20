<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;

final class RouteContext extends AbstractContext implements Context
{
    private $entityManager;

    private $routeFactory;

    private $routeRepository;

    private $routeService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouteFactoryInterface $routeFactory,
        RouteRepositoryInterface $routeRepository,
        RouteServiceInterface $routeService
    ) {
        $this->entityManager = $entityManager;
        $this->routeFactory = $routeFactory;
        $this->routeRepository = $routeRepository;
        $this->routeService = $routeService;
    }

    /**
     * @Given the following Routes:
     */
    public function theFollowingRoutes(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            /** @var RouteInterface $route */
            $route = $this->routeFactory->create();
            $this->fillObject($route, $columns);
            $this->routeService->createRoute($route);

            $this->entityManager->persist($route);
        }

        $this->entityManager->flush();
    }
}
