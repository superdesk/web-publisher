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

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
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
        $request->attributes->set(LoaderInterface::PREVIEW_MODE, true);
        $mediaFactory = $this->get('swp.factory.media');
        /** @var RouteInterface $route */
        $route = $this->findRouteOr404($routeId);
        /** @var PackageInterface $package */
        $package = $this->findPackageOr404($id);
        $article = $this->get('swp.factory.article')->createFromPackage($package);
        $this->fillArticleMedia($mediaFactory, $package, $article);

        $metaFactory = $this->get('swp_template_engine_context.factory.meta_factory');
        $templateEngineContext = $this->get('swp_template_engine_context');
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
     * @param MediaFactoryInterface $mediaFactory
     * @param PackageInterface      $package
     * @param ArticleInterface      $article
     */
    private function fillArticleMedia(MediaFactoryInterface $mediaFactory, PackageInterface $package, ArticleInterface $article)
    {
        if (null === $package || (null !== $package && 0 === count($package->getItems()))) {
            return;
        }

        $articleMedia = new ArrayCollection();
        foreach ($package->getItems() as $packageItem) {
            $key = $packageItem->getName();
            if (ItemInterface::TYPE_PICTURE === $packageItem->getType() || ItemInterface::TYPE_FILE === $packageItem->getType()) {
                $articleMedia->add($this->handleMedia($mediaFactory, $article, $key, $packageItem));
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_FILE === $item->getType()) {
                        $articleMedia->add($this->handleMedia($mediaFactory, $article, $key, $item));
                    }
                }
            }
        }

        $article->setMedia($articleMedia);
    }

    /**
     * @param MediaFactoryInterface $mediaFactory
     * @param ArticleInterface      $article
     * @param string                $key
     * @param ItemInterface         $item
     *
     * @return \SWP\Bundle\ContentBundle\Model\ArticleMediaInterface
     */
    private function handleMedia(MediaFactoryInterface $mediaFactory, ArticleInterface $article, string $key, ItemInterface $item)
    {
        $articleMedia = $mediaFactory->create($article, $key, $item);
        if (ItemInterface::TYPE_PICTURE === $item->getType()) {
            $mediaFactory->replaceBodyImagesWithMedia($article, $articleMedia);
        }

        if (ArticleInterface::KEY_FEATURE_MEDIA === $key) {
            $article->setFeatureMedia($articleMedia);
        }

        return $articleMedia;
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
