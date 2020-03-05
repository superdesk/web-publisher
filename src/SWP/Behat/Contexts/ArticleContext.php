<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Transliterator\Transliterator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\Lorem;
use Faker\Provider\Uuid;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;

final class ArticleContext extends AbstractContext implements Context
{
    private $articleFactory;

    private $entityManager;

    private $routeFactory;

    private $routeRepository;

    private $routeService;

    public function __construct(
        ArticleFactoryInterface $articleFactory,
        EntityManagerInterface $entityManager,
        RouteFactoryInterface $routeFactory,
        RouteRepositoryInterface $routeRepository,
        RouteServiceInterface $routeService
    ) {
        $this->articleFactory = $articleFactory;
        $this->entityManager = $entityManager;
        $this->routeFactory = $routeFactory;
        $this->routeRepository = $routeRepository;
        $this->routeService = $routeService;
    }

    /**
     * @Given the following Articles:
     */
    public function theFollowingArticles(TableNode $table)
    {
        foreach ($table as $row => $columns) {
            /** @var ArticleInterface $article */
            $article = $this->articleFactory->create();
            $article->setLocale('en');
            $article->setCode(Uuid::uuid());

            $columns['route'] = $this->getRoute($columns['route']);
            if (!isset($columns['body'])) {
                $columns['body'] = implode(' ', Lorem::paragraphs(3));
            }

            $this->fillObject($article, $columns);
            $this->entityManager->persist($article);
        }

        $this->entityManager->flush();
    }

    private function getRoute(string $routeName): RouteInterface
    {
        /** @var RouteInterface $route */
        $route = $this->routeRepository->findOneBy(['name' => $routeName]);
        if (null !== $route) {
            return $route;
        }

        /** @var RouteInterface $route */
        $route = $this->routeFactory->create();
        $route->setName($routeName);
        $route->setSlug(Transliterator::urlize($routeName));
        $route->setType(RouteInterface::TYPE_COLLECTION);
        $this->routeService->createRoute($route);
        $this->entityManager->persist($route);
        $this->entityManager->flush();

        return $route;
    }
}
