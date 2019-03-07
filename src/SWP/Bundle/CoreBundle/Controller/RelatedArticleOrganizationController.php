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
use SWP\Bundle\CoreBundle\Model\RelatedArticleList;
use SWP\Bundle\CoreBundle\Model\RelatedArticleListItem;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\GroupInterface;
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

        $relatedItemsGroups = $package->getGroups()->filter(function ($group) {
            return GroupInterface::TYPE_RELATED === $group->getType();
        });

        if (null === $package || (null !== $package && 0 === \count($relatedItemsGroups))) {
            return;
        }

        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $articleRepository = $this->get('swp.repository.article');

        $relatedArticlesList = new RelatedArticleList();
        foreach ($relatedItemsGroups as $relatedItemsGroup) {
            foreach ($relatedItemsGroup->getItems() as $item) {
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
        }

        return new SingleResourceResponse($relatedArticlesList);
    }
}
