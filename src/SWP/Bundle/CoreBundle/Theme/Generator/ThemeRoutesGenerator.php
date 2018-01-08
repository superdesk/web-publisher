<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Generator;

use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Form\Type\RouteType;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ThemeRoutesGenerator implements GeneratorInterface
{
    /**
     * @var RouteServiceInterface
     */
    protected $routeService;

    /**
     * @var RouteRepositoryInterface
     */
    protected $routeRepository;

    /**
     * @var RouteProviderInterface
     */
    protected $routeProvider;

    /**
     * @var RouteFactoryInterface
     */
    protected $routeFactory;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FakeArticlesGeneratorInterface
     */
    protected $fakeArticlesGenerator;

    /**
     * ThemeRoutesGenerator constructor.
     *
     * @param RouteServiceInterface          $routeService
     * @param RouteRepositoryInterface       $routeRepository
     * @param RouteProviderInterface         $routeProvider
     * @param RouteFactoryInterface          $routeFactory
     * @param FormFactoryInterface           $formFactory
     * @param FakeArticlesGeneratorInterface $fakeArticlesGenerator
     */
    public function __construct(RouteServiceInterface $routeService, RouteRepositoryInterface $routeRepository, RouteProviderInterface $routeProvider, RouteFactoryInterface $routeFactory, FormFactoryInterface $formFactory, FakeArticlesGeneratorInterface $fakeArticlesGenerator)
    {
        $this->routeService = $routeService;
        $this->routeRepository = $routeRepository;
        $this->routeProvider = $routeProvider;
        $this->routeFactory = $routeFactory;
        $this->formFactory = $formFactory;
        $this->fakeArticlesGenerator = $fakeArticlesGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $routes): void
    {
        foreach ($routes as $routeData) {
            $cleanRouteData = $routeData;
            unset($cleanRouteData['numberOfArticles']);
            $route = $this->createRoute($cleanRouteData);
            if (null !== $this->routeProvider->getOneByStaticPrefix($route->getStaticPrefix())) {
                continue;
            }

            $this->processFakeArticles($route, $routeData);

            $this->routeRepository->add($route);
        }
    }

    /**
     * @param array $routeData
     *
     * @return RouteInterface
     *
     * @throws \Exception
     */
    protected function createRoute(array $routeData): RouteInterface
    {
        /** @var RouteInterface $route */
        $route = $this->routeFactory->create();

        if (null !== $routeData['parent']) {
            if (null !== $parent = $this->routeProvider->getRouteByName($routeData['parent'])) {
                $route->setParent($parent);
            }

            unset($routeData['parent']);
        }

        $form = $this->formFactory->create(RouteType::class, $route);
        $form->submit($routeData, false);

        if ($form->isValid()) {
            $this->routeService->createRoute($route);
        } else {
            throw new \Exception('Invalid route definition');
        }

        return $route;
    }

    /**
     * @param RouteInterface $route
     * @param array          $routeData
     */
    protected function processFakeArticles(RouteInterface $route, array $routeData)
    {
        if (null !== $routeData['numberOfArticles']) {
            $articles = $this->fakeArticlesGenerator->generate($routeData['numberOfArticles']);
            foreach ($articles as $article) {
                $route->addArticle($article);
            }
        }
        unset($routeData['numberOfArticles']);
    }
}
