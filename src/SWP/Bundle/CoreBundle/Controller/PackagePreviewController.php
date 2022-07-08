<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Context\ArticlePreviewContext;
use SWP\Bundle\CoreBundle\Context\ArticlePreviewContextInterface;
use SWP\Bundle\CoreBundle\Factory\PackagePreviewTokenFactoryInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticlePreview;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\PackagePreviewTokenInterface;
use SWP\Bundle\CoreBundle\Repository\PackageRepositoryInterface;
use SWP\Bundle\CoreBundle\Resolver\TemplateNameResolverInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePreviewerInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Route as FOSRoute;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Error\LoaderError;

class PackagePreviewController extends Controller {

  private EventDispatcherInterface $eventDispatcher;
  private DataTransformerInterface $dataTransformer;
  private PackageRepositoryInterface $packageRepository;
  private RouteRepositoryInterface $routeRepository;
  private RepositoryInterface $packagePreviewTokenRepository;
  private PackagePreviewTokenFactoryInterface $packagePreviewTokenFactory;
  private TemplateNameResolverInterface $templateNameResolver;
  private ArticlePreviewerInterface $articlePreviewer;
  private ArticlePreviewContextInterface $articlePreviewContext;

  /**
   * @param EventDispatcherInterface $eventDispatcher
   * @param DataTransformerInterface $dataTransformer
   * @param PackageRepositoryInterface $packageRepository
   * @param RouteRepositoryInterface $routeRepository
   * @param RepositoryInterface $packagePreviewTokenRepository
   * @param PackagePreviewTokenFactoryInterface $packagePreviewTokenFactory
   * @param TemplateNameResolverInterface $templateNameResolver
   * @param ArticlePreviewerInterface $articlePreviewer
   * @param ArticlePreviewContextInterface $articlePreviewContext
   */
  public function __construct(EventDispatcherInterface            $eventDispatcher,
                              DataTransformerInterface            $dataTransformer,
                              PackageRepositoryInterface          $packageRepository,
                              RouteRepositoryInterface            $routeRepository,
                              RepositoryInterface                 $packagePreviewTokenRepository,
                              PackagePreviewTokenFactoryInterface $packagePreviewTokenFactory,
                              TemplateNameResolverInterface       $templateNameResolver,
                              ArticlePreviewerInterface           $articlePreviewer,
                              ArticlePreviewContextInterface      $articlePreviewContext) {
    $this->eventDispatcher = $eventDispatcher;
    $this->dataTransformer = $dataTransformer;
    $this->packageRepository = $packageRepository;
    $this->routeRepository = $routeRepository;
    $this->packagePreviewTokenRepository = $packagePreviewTokenRepository;
    $this->packagePreviewTokenFactory = $packagePreviewTokenFactory;
    $this->templateNameResolver = $templateNameResolver;
    $this->articlePreviewer = $articlePreviewer;
    $this->articlePreviewContext = $articlePreviewContext;
  }


  /**
   * @Route("/preview/package/{routeId}/{id}", options={"expose"=true}, requirements={"id"="\d+", "routeId"="\d+", "token"=".+"}, methods={"GET"}, name="swp_package_preview")
   */
  public function previewAction(int $routeId, $id) {
    /** @var RouteInterface $route */
    $route = $this->findRouteOr404($routeId);
    /** @var PackageInterface $package */
    $package = $this->findPackageOr404($id);
    $articlePreviewer = $this->articlePreviewer;
    $article = $articlePreviewer->preview($package, $route);

    $articlePreview = new ArticlePreview();
    $articlePreview->setArticle($article);

    $this->eventDispatcher->dispatch(new GenericEvent($articlePreview), ArticleEvents::PREVIEW);

    if (null !== ($url = $articlePreview->getPreviewUrl())) {
      return new RedirectResponse($url);
    }

    $route = $this->ensureRouteTemplateExists($route, $article);

    try {
      return $this->render($route->getArticlesTemplateName());
    } catch (\Exception $e) {
      throw $this->createNotFoundException(sprintf('Template for route with id "%d" (%s) not found!', $route->getId(), $route->getName()));
    }
  }

  /**
   * @FOSRoute("/api/{version}/preview/package/generate_token/{routeId}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_preview_package_token", requirements={"routeId"="\d+"})
   */
  public function generateTokenAction(Request $request, int $routeId): SingleResourceResponseInterface {
    $route = $this->findRouteOr404($routeId);

    /** @var string $content */
    $content = (string)$request->getContent();
    $dispatcher = $this->eventDispatcher;
    $package = $this->dataTransformer->transform($content);
    $dispatcher->dispatch(new GenericEvent($package), Events::SWP_VALIDATION);

    $tokenRepository = $this->packagePreviewTokenRepository;
    $existingPreviewToken = $tokenRepository->findOneBy(['route' => $route]);

    if (null === $existingPreviewToken) {
      $packagePreviewToken = $this->packagePreviewTokenFactory->createTokenizedWith($route, $content);

      $tokenRepository->persist($packagePreviewToken);
      $tokenRepository->flush();

      return $this->returnResponseWithPreviewUrl($packagePreviewToken);
    }

    $this->updatePackagePreviewTokenBody($content, $existingPreviewToken);

    return $this->returnResponseWithPreviewUrl($existingPreviewToken);
  }

  /**
   * @Route("/preview/publish/package/{token}", options={"expose"=true}, requirements={"token"=".+"}, methods={"GET"}, name="swp_package_preview_publish")
   */
  public function publishPreviewAction(string $token) {
    $existingPreviewToken = $this->packagePreviewTokenRepository->findOneBy(['token' => $token]);

    if (null === $existingPreviewToken) {
      throw $this->createNotFoundException(sprintf('Token %s is not valid.', $token));
    }

    $article = $this->getArticleForPreview($existingPreviewToken);
    $route = $article->getRoute();
    $route = $this->ensureRouteTemplateExists($route, $article);

    return $this->renderTemplateOr404($route);
  }

  private function updatePackagePreviewTokenBody(string $content, PackagePreviewTokenInterface $packagePreviewToken) {
    if (md5($content) !== md5($packagePreviewToken->getBody())) {
      $packagePreviewToken->setBody($content);

      $this->packagePreviewTokenRepository->flush();
    }
  }

  private function returnResponseWithPreviewUrl(PackagePreviewTokenInterface $packagePreviewToken): SingleResourceResponseInterface {
    $article = $this->getArticleForPreview($packagePreviewToken);
    $articlePreview = new ArticlePreview();
    $articlePreview->setArticle($article);

    $this->eventDispatcher->dispatch(new GenericEvent($articlePreview), ArticleEvents::PREVIEW);

    $url = $articlePreview->getPreviewUrl();

    if (null === $url) {
      $url = $this->generateUrl(
          'swp_package_preview_publish',
          ['token' => $packagePreviewToken->getToken()],
          UrlGeneratorInterface::ABSOLUTE_URL
      );
    }

    return new SingleResourceResponse([
        'preview_url' => $url,
    ]);
  }

  private function getArticleForPreview(PackagePreviewTokenInterface $packagePreviewToken): ArticleInterface {
    $dispatcher = $this->eventDispatcher;
    $package = $this->dataTransformer->transform($packagePreviewToken->getBody());
    $dispatcher->dispatch(new GenericEvent($package), Events::SWP_VALIDATION);

    $articlePreviewer = $this->articlePreviewer;
    $articlePreviewContext = $this->articlePreviewContext;

    $articlePreviewContext->setIsPreview(true);

    return $articlePreviewer->preview($package, $packagePreviewToken->getRoute());
  }

  private function renderTemplateOr404(RouteInterface $route): Response {
    try {
      return $this->render($templateName = $route->getArticlesTemplateName());
    } catch (\InvalidArgumentException | LoaderError $e) {
      throw $this->createNotFoundException(sprintf('Template %s for route with id "%d" (%s) not found!', $templateName, $route->getId(), $route->getName()));
    }
  }

  private function ensureRouteTemplateExists(RouteInterface $route, ArticleInterface $article): RouteInterface {
    if (null === $route->getArticlesTemplateName()) {
      $templateNameResolver = $this->templateNameResolver;
      $route->setArticlesTemplateName($templateNameResolver->resolve($article));
    }

    return $route;
  }

  private function findRouteOr404(int $id): RouteInterface {
    /** @var RouteInterface $route */
    if (null === ($route = $this->routeRepository->findOneBy(['id' => $id]))) {
      throw $this->createNotFoundException(sprintf('Route with id: "%s" not found!', $id));
    }

    return $route;
  }

  private function findPackageOr404(string $id): PackageInterface {
    /** @var PackageInterface $package */
    if (null === ($package = $this->packageRepository->findOneBy(['id' => $id]))) {
      throw $this->createNotFoundException(sprintf('Package with id: "%s" not found!', $id));
    }

    return $package;
  }
}
