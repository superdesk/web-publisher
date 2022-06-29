<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\RelatedArticleList;
use SWP\Bundle\CoreBundle\Model\RelatedArticleListItem;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Bundle\CoreBundle\Repository\PackageRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class RelatedArticleOrganizationController extends Controller {
  private EventDispatcherInterface $eventDispatcher;
  private DataTransformerInterface $dataTransformer;
  private CachedTenantContextInterface $cachedTenantContext;
  private PackageRepositoryInterface $packageRepository;
  private ArticleRepositoryInterface $articleRepository;
  private TenantRepositoryInterface $tenantRepository;

  /**
   * @param EventDispatcherInterface $eventDispatcher
   * @param DataTransformerInterface $dataTransformer
   * @param CachedTenantContextInterface $cachedTenantContext
   * @param PackageRepositoryInterface $packageRepository
   * @param ArticleRepositoryInterface $articleRepository
   * @param TenantRepositoryInterface $tenantRepository
   */
  public function __construct(EventDispatcherInterface     $eventDispatcher,
                              DataTransformerInterface     $dataTransformer,
                              CachedTenantContextInterface $cachedTenantContext,
                              PackageRepositoryInterface   $packageRepository,
                              ArticleRepositoryInterface   $articleRepository,
                              TenantRepositoryInterface    $tenantRepository) {
    $this->eventDispatcher = $eventDispatcher;
    $this->dataTransformer = $dataTransformer;
    $this->cachedTenantContext = $cachedTenantContext;
    $this->packageRepository = $packageRepository;
    $this->articleRepository = $articleRepository;
    $this->tenantRepository = $tenantRepository;
  }


  /**
   * @Route("/api/{version}/organization/articles/related/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_organization_related_articles")
   */
  public function postAction(Request $request) {
    $content = $request->getContent();
    $package = $this->dataTransformer->transform($content);
    $relatedArticlesList = $this->getRelated($package);

    return new SingleResourceResponse($relatedArticlesList);
  }

  /**
   * @Route("/api/{version}/packages/{id}/related/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_packages_related_articles", requirements={"id"="\d+"})
   */
  public function getRelatedAction(string $id) {
    $package = $this->findOr404((int)$id);

    $relatedArticlesList = $this->getRelated($package);

    return new SingleResourceResponse($relatedArticlesList);
  }

  private function getRelated(PackageInterface $package): RelatedArticleList {
    $relatedItemsGroups = $package->getItems()->filter(static function ($group) {
      return ItemInterface::TYPE_TEXT === $group->getType();
    });

    $relatedArticlesList = new RelatedArticleList();

    if (null === $package || (null !== $package && 0 === \count($relatedItemsGroups))) {
      return $relatedArticlesList;
    }

    $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);
    $articleRepository = $this->articleRepository;

    foreach ($relatedItemsGroups as $item) {
      if (null === ($existingArticles = $articleRepository->findBy(['code' => $item->getGuid()]))) {
        continue;
      }

      $tenants = [];
      foreach ($existingArticles as $existingArticle) {
        $tenantCode = $existingArticle->getTenantCode();
        $tenant = $this->tenantRepository->findOneByCode($tenantCode);

        $tenants[] = $tenant;
      }

      $relatedArticleListItem = new RelatedArticleListItem();
      $relatedArticleListItem->setTenants($tenants);
      $relatedArticleListItem->setTitle($item->getHeadline());

      $relatedArticlesList->addRelatedArticleItem($relatedArticleListItem);
    }

    return $relatedArticlesList;
  }

  private function findOr404(int $id): PackageInterface {
    $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);
    $tenantContext = $this->cachedTenantContext;
    if (null === $package = $this->packageRepository->findOneBy(['id' => $id, 'organization' => $tenantContext->getTenant()->getOrganization()])) {
      throw new NotFoundHttpException('Package was not found.');
    }

    return $package;
  }
}
