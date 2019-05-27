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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\RelatedArticleList;
use SWP\Bundle\CoreBundle\Model\RelatedArticleListItem;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RelatedArticleOrganizationController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Returns a list of related articles",
     *     statusCodes={
     *         200="Returned on success"
     *     }
     * )
     * @Route("/api/{version}/organization/articles/related/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_organization_related_articles")
     * @Method("POST")
     */
    public function getAction(Request $request)
    {
        $content = $request->getContent();
        $package = $this->get('swp_bridge.transformer.json_to_package')->transform($content);

        $relatedArticlesList = $this->getRelated($package);

        return new SingleResourceResponse($relatedArticlesList);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Returns a list of related articles",
     *     statusCodes={
     *         200="Returned on success"
     *     }
     * )
     * @Route("/api/{version}/packages/{id}/related/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_packages_related_articles", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function getRelatedAction(string $id)
    {
        $package = $this->findOr404((int) $id);

        $relatedArticlesList = $this->getRelated($package);

        return new SingleResourceResponse($relatedArticlesList);
    }

    private function getRelated(PackageInterface $package): RelatedArticleList
    {
        $relatedItemsGroups = $package->getItems()->filter(static function ($group) {
            return ItemInterface::TYPE_TEXT === $group->getType();
        });

        $relatedArticlesList = new RelatedArticleList();

        if (null === $package || (null !== $package && 0 === \count($relatedItemsGroups))) {
            return $relatedArticlesList;
        }

        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $articleRepository = $this->get('swp.repository.article');

        foreach ($relatedItemsGroups as $item) {
            if (null === ($existingArticles = $articleRepository->findBy(['code' => $item->getGuid()]))) {
                continue;
            }

            $tenants = [];
            foreach ($existingArticles as $existingArticle) {
                $tenantCode = $existingArticle->getTenantCode();
                $tenant = $this->get('swp.repository.tenant')->findOneByCode($tenantCode);

                $tenants[] = $tenant;
            }

            $relatedArticleListItem = new RelatedArticleListItem();
            $relatedArticleListItem->setTenants($tenants);
            $relatedArticleListItem->setTitle($item->getHeadline());

            $relatedArticlesList->addRelatedArticleItem($relatedArticleListItem);
        }

        return $relatedArticlesList;
    }

    private function findOr404(int $id): PackageInterface
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
        if (null === $package = $this->get('swp.repository.package')->findOneBy(['id' => $id, 'organization' => $tenantContext->getTenant()->getOrganization()])) {
            throw new NotFoundHttpException('Package was not found.');
        }

        return $package;
    }
}
