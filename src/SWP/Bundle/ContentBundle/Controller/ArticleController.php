<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use SWP\Bundle\ContentBundle\Form\Type\ArticleType;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/api/{version}/content/articles/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_list_articles")
     *
     * @throws \Exception
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $authors = '';
        if (null !== $request->query->get('author', null)) {
            $authors = explode(', ', $request->query->get('author'));
        }

        if ($request->query->get('route', false) && $request->query->get('includeSubRoutes', false)) {
            $routeObject = $this->get('swp.provider.route')->getOneById($request->query->get('route'));

            if (null !== $routeObject) {
                $ids = [$routeObject->getId()];
                foreach ($routeObject->getChildren() as $child) {
                    $ids[] = $child->getId();
                }
                $request->query->set('route', $ids);
            }
        }

        $articles = $this->get('swp.repository.article')
            ->getPaginatedByCriteria(new Criteria([
                'status' => $request->query->get('status', ''),
                'route' => $request->query->get('route', ''),
                'publishedBefore' => $request->query->has('publishedBefore') ? new \DateTime($request->query->get('publishedBefore')) : null,
                'publishedAfter' => $request->query->has('publishedAfter') ? new \DateTime($request->query->get('publishedAfter')) : null,
                'author' => $authors,
                'query' => $request->query->get('query', ''),
                'source' => $request->query->get('source', []),
            ]), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($articles);
    }

    /**
     * @Route("/api/{version}/content/articles/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_show_articles", requirements={"id"=".+"})
     */
    public function getAction($id): SingleResourceResponseInterface
    {
        $article = $this->get('swp.provider.article')->getOneById($id);

        if (null === $article) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return new SingleResourceResponse($article);
    }

    /**
     * @Route("/api/{version}/content/articles/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_update_articles", requirements={"id"=".+"})
     */
    public function updateAction(Request $request, $id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.article');
        $article = $this->findOr404($id);
        $originalArticleStatus = $article->getStatus();

        $form = $this->get('form.factory')->createNamed('', ArticleType::class, $article, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swp.service.article')->reactOnStatusChange($originalArticleStatus, $article);
            $objectManager->flush();
            $objectManager->refresh($article);

            return new SingleResourceResponse($article);
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    /**
     * @Route("/api/{version}/content/articles/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_delete_articles", requirements={"id"=".+"})
     */
    public function deleteAction($id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.article');
        $objectManager->remove($this->findOr404($id));
        $objectManager->flush();

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    private function findOr404($id)
    {
        if (null === $article = $this->get('swp.provider.article')->getOneById($id)) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $article;
    }
}
