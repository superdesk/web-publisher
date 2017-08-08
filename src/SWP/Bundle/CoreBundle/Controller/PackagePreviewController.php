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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PackagePreviewController extends Controller
{
    /**
     * @Route("/preview/package/{routeId}/{id}", options={"expose"=true}, requirements={"slug"=".+", "routeId"="\d+", "token"=".+"}, name="swp_package_preview")
     * @Method("GET")
     */
    public function previewAction(Request $request, int $routeId, $id)
    {
        $mediaFactory = $this->get('swp.factory.media');
        /** @var RouteInterface $route */
        $route = $this->findRouteOr404($routeId);
        /** @var PackageInterface $package */
        $package = $this->findPackageOr404($id);
        $article = $this->get('swp.factory.article')->createFromPackage($package);
        $this->get('swp_content_bundle.processor.article_body')->fillArticleMedia($mediaFactory, $package, $article);

        $metaFactory = $this->get('swp_template_engine_context.factory.meta_factory');
        $templateEngineContext = $this->get('swp_template_engine_context');
        $templateEngineContext->setPreviewMode(true);
        $templateEngineContext->setCurrentPage($metaFactory->create($route));
        $templateEngineContext->getMetaForValue($article);

        if (null === $route->getArticlesTemplateName()) {
            throw $this->createNotFoundException(
                sprintf('Template for route with id "%d" (%s) not found!', $route->getId(), $route->getName())
            );
        }

        return $this->render($route->getArticlesTemplateName());
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
