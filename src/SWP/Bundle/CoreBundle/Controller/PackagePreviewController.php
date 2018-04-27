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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\PackagePreviewTokenInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePreviewer;
use SWP\Component\Bridge\Events;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PackagePreviewController extends Controller
{
    /**
     * @Route("/preview/package/{routeId}/{id}", options={"expose"=true}, requirements={"id"="\d+", "routeId"="\d+", "token"=".+"}, name="swp_package_preview")
     * @Method("GET")
     */
    public function previewAction(Request $request, int $routeId, $id)
    {
        /** @var RouteInterface $route */
        $route = $this->findRouteOr404($routeId);
        /** @var PackageInterface $package */
        $package = $this->findPackageOr404($id);
        $article = $this->get('swp.factory.article')->createFromPackage($package);
        $this->get('swp_content_bundle.processor.article_body')->fillArticleMedia($package, $article);

        $article->setRoute($route);
        $metaFactory = $this->get('swp_template_engine_context.factory.meta_factory');
        $templateEngineContext = $this->get('swp_template_engine_context');
        $templateEngineContext->setPreviewMode(true);
        $templateEngineContext->setCurrentPage($metaFactory->create($route));
        $templateEngineContext->getMetaForValue($article);

        $route = $this->ensureRouteTemplateExists($route, $article);

        try {
            return $this->render($route->getArticlesTemplateName());
        } catch (\Exception $e) {
            throw $this->createNotFoundException(
                sprintf('Template for route with id "%d" (%s) not found!', $route->getId(), $route->getName())
            );
        }
    }

    /**
     * Generates package preview token for specific route.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Generate package preview token for specific route",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     }
     * )
     * @Route("/api/{version}/preview/package/generate_token/{routeId}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_preview_package_token", requirements={"routeId"="\d+"})
     * @Method("POST")
     */
    public function generateTokenAction(Request $request, int $routeId)
    {
        $route = $this->findRouteOr404($routeId);

        /** @var string $content */
        $content = (string) $request->getContent();
        $dispatcher = $this->get('event_dispatcher');
        $package = $this->get('swp_bridge.transformer.json_to_package')->transform($content);
        $dispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($package));

        $existingPreviewToken = $this->get('swp.repository.package_preview_token')->findOneBy(['route' => $route]);

        if (null === $existingPreviewToken) {
            $packagePreviewToken = $this->get('swp.factory.package_preview_token')->createTokenizedWith($route, $content);

            $this->get('swp.repository.package_preview_token')->persist($packagePreviewToken);
            $this->get('swp.repository.package_preview_token')->flush();

            return $this->returnResponseWithPreviewUrl($packagePreviewToken);
        }

        $this->updatePackagePreviewTokenBody($content, $existingPreviewToken);

        return $this->returnResponseWithPreviewUrl($existingPreviewToken);
    }

    private function updatePackagePreviewTokenBody(string $content, PackagePreviewTokenInterface $packagePreviewToken)
    {
        if (md5($content) !== md5($packagePreviewToken->getBody())) {
            $packagePreviewToken->setBody($content);

            $this->get('swp.repository.package_preview_token')->flush();
        }
    }

    private function returnResponseWithPreviewUrl(PackagePreviewTokenInterface $packagePreviewToken): SingleResourceResponseInterface
    {
        $url = $this->generateUrl(
            'swp_package_preview_publish',
            ['token' => $packagePreviewToken->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new SingleResourceResponse([
            'preview_url' => $url,
        ]);
    }

    /**
     * @Route("/preview/publish/package/{token}", options={"expose"=true}, requirements={"token"=".+"}, name="swp_package_preview_publish")
     * @Method("GET")
     */
    public function publishPreviewAction(string $token)
    {
        $existingPreviewToken = $this->get('swp.repository.package_preview_token')->findOneBy(['token' => $token]);

        if (null === $existingPreviewToken) {
            throw $this->createNotFoundException(sprintf('Token %s is not valid.', $token));
        }

        $dispatcher = $this->get('event_dispatcher');
        $package = $this->get('swp_bridge.transformer.json_to_package')->transform($existingPreviewToken->getBody());
        $dispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($package));

        $articlePreviewer = $this->get(ArticlePreviewer::class);

        $article = $articlePreviewer->preview($package, $existingPreviewToken->getRoute());
        $route = $article->getRoute();
        $route = $this->ensureRouteTemplateExists($route, $article);

        return $this->renderTemplateOr404($route);
    }

    private function renderTemplateOr404(RouteInterface $route): Response
    {
        try {
            return $this->render($route->getArticlesTemplateName());
        } catch (\Exception $e) {
            throw $this->createNotFoundException(
                sprintf('Template for route with id "%d" (%s) not found!', $route->getId(), $route->getName())
            );
        }
    }

    private function ensureRouteTemplateExists(RouteInterface $route, ArticleInterface $article): RouteInterface
    {
        if (null === $route->getArticlesTemplateName()) {
            $templateNameResolver = $this->get('swp_core.theme.resolver.template_name');
            $route->setArticlesTemplateName($templateNameResolver->resolve($article));
        }

        return $route;
    }

    /**
     * @param int $id
     *
     * @return null|object
     */
    private function findRouteOr404(int $id)
    {
        if (null === ($route = $this->get('swp.repository.route')->findOneBy(['id' => $id]))) {
            throw $this->createNotFoundException(sprintf('Route with id: "%s" not found!', $id));
        }

        return $route;
    }

    /**
     * @param string $id
     *
     * @return null|object
     */
    private function findPackageOr404(string $id)
    {
        if (null === ($package = $this->get('swp.repository.package')->findOneBy(['id' => $id]))) {
            throw $this->createNotFoundException(sprintf('Package with id: "%s" not found!', $id));
        }

        return $package;
    }
}
