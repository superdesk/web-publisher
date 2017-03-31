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

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentBundle\Form\Type\ArticleType;
use SWP\Bundle\CoreBundle\Form\Type\MultiplePublishType;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrganizationController extends Controller
{
    /**
     * List all organizations.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all organizations",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error occurred."
     *     }
     * )
     * @Route("/api/{version}/organizations/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_organizations")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $organizations = $this->get('swp.repository.organization')
            ->getPaginatedByCriteria(new Criteria(), [], new PaginationData($request));

        return new ResourcesListResponse($organizations);
    }

    /**
     * List all current organization's articles.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all current organization's articles",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error occurred."
     *     },
     *     filters={
     *         {"name"="status", "dataType"="string", "pattern"="new|published|unpublished|canceled"}
     *     }
     * )
     * @Route("/api/{version}/organization/articles/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_organization_articles")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function articlesAction(Request $request)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
        $repository = $this->get('swp.repository.article');
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');

        $items = $repository->getPaginatedByCriteria(
            new Criteria([
                'organization' => $tenantContext->getTenant()->getOrganization(),
                'status' => $request->query->get('status', ''),
            ]),
            [],
            new PaginationData($request)
        );

        $entityManager->getFilters()->enable('tenantable');

        return new ResourcesListResponse($items);
    }

    /**
     * Updates organization's article.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Updates organization's article",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\ArticleType"
     * )
     * @Route("/api/{version}/organization/articles/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_organization_articles", requirements={"id"="\d+"})
     * @Method("PATCH")
     */
    public function updateArticleAction(Request $request, int $id)
    {
        $objectManager = $this->get('swp.object_manager.article');
        $article = $this->getOrganizationArticle($id);
        $originalArticleStatus = $article->getStatus();

        $form = $this->createForm(ArticleType::class, $article, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('swp.service.article')->reactOnStatusChange($originalArticleStatus, $article);
            $objectManager->flush();
            $objectManager->refresh($article);

            return new SingleResourceResponse($article);
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    /**
     * Show single tenant article.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single organization article",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/organization/articles/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_show_organization_article", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction(int $id)
    {
        return new SingleResourceResponse($this->getOrganizationArticle($id));
    }

    /**
     * Publishes article to many websites.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Publishes article to many tenants",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\MultiplePublishType"
     * )
     * @Route("/api/{version}/organization/articles/{id}/publish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_publish_organization_articles", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function publishAction(Request $request, int $id)
    {
        $article = $this->getOrganizationArticle($id);
        $form = $this->createForm(MultiplePublishType::class, null, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();

            $this->get('swp_core.article.publisher')->publish($article, $formData['tenants']);

            return new SingleResourceResponse(null, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    private function getOrganizationArticle(int $id)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');
        $article = $this->findOr404($id);
        $entityManager->getFilters()->enable('tenantable');

        return $article;
    }

    private function findOr404(int $id)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        if (null === $article = $this->get('swp.repository.article')->findOneBy([
                'id' => $id,
                'organization' => $tenantContext->getTenant()->getOrganization(),
            ])) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $article;
    }
}
