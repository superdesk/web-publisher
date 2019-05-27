<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListItemsType;
use SWP\Bundle\CoreBundle\Form\Type\ContentListItemType;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentListItemController extends Controller
{
    /**
     * List all items of content list.
     *
     * @Operation(
     *     tags={""},
     *     summary="Lists content list items",
     *     @SWG\Parameter(
     *         name="sticky",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Content list item not found."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/content/lists/{id}/items/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_items", requirements={"id"="\d+"})
     */
    public function listAction(Request $request, $id)
    {
        $repository = $this->get('swp.repository.content_list_item');

        $items = $repository->getPaginatedByCriteria(
            new Criteria([
                'contentList' => $id,
                'sticky' => $request->query->get('sticky', ''),
            ]),
            $request->query->get('sorting', ['sticky' => 'desc']),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    /**
     * @Operation(
     *     tags={""},
     *     summary="Get single content list item",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/content/lists/{listId}/items/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_show_lists_item", requirements={"id"="\d+"})
     */
    public function getAction($listId, $id)
    {
        return new SingleResourceResponse($this->findOr404($listId, $id));
    }

    /**
     * @Operation(
     *     tags={""},
     *     summary="Update single content list item",
     *     @SWG\Parameter(
     *         name="sticky",
     *         in="body",
     *         description="Defines whether content is sticky or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when not valid data."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found."
     *     )
     * )
     *
     * @Route("/api/{version}/content/lists/{listId}/items/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_lists_item", requirements={"id"="\d+", "listId"="\d+"})
     */
    public function updateAction(Request $request, $listId, $id)
    {
        $objectManager = $this->get('swp.object_manager.content_list_item');
        $contentListItem = $this->findOr404($listId, $id);
        $form = $this->get('form.factory')->createNamed('',
            ContentListItemType::class,
            $contentListItem,
            ['method' => $request->getMethod()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contentListItem->getContentList()->setUpdatedAt(new \DateTime());
            $objectManager->flush();

            return new SingleResourceResponse($contentListItem);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Tips:
     *  - position "-1" will place element at end of list.
     *  - make sure that "updated_at" value is filled with value fetched from list.
     *
     * Possible actions: move, add, delete
     *
     * @Operation(
     *     tags={""},
     *     summary="Update many content list items",
     *     @SWG\Parameter(
     *         name="items",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="array of objects (ContentListItemPositionType)")
     *     ),
     *     @SWG\Parameter(
     *         name="updatedAt",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when not valid data."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found."
     *     )
     * )
     *
     * @Route("/api/{version}/content/lists/{listId}/items/", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_batch_update_lists_item", requirements={"listId"="\d+"})
     */
    public function batchUpdateAction(Request $request, $listId)
    {
        /** @var ContentListInterface $list */
        $list = $this->get('swp.repository.content_list')->findOneBy([
            'id' => $listId,
        ]);

        if (null === $list) {
            throw new NotFoundHttpException(sprintf('Content list with id "%s" was not found.', $list));
        }

        $objectManager = $this->get('swp.object_manager.content_list_item');
        $form = $this->get('form.factory')->createNamed('', ContentListItemsType::class, [], ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $updatedAt = \DateTime::createFromFormat(\DateTime::RFC3339, $data['updatedAt'], new \DateTimeZone('UTC'));
            $updatedAt->setTimezone(new \DateTimeZone('UTC'));
            $listUpdatedAt = $list->getUpdatedAt();
            $listUpdatedAt->setTimezone(new \DateTimeZone('UTC'));
            if ($updatedAt < $listUpdatedAt) {
                throw new ConflictHttpException('List was already updated');
            }

            $updatedArticles = [];
            foreach ($data['items'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (!isset($item['position']) || !is_numeric($item['position'])) {
                    $item['position'] = 0;
                }

                switch ($item['action']) {
                    case 'move':
                        $contentListItem = $this->findByContentOr404($list, $item['contentId']);
                        $contentListItem->setPosition($item['position']);
                        $list->setUpdatedAt(new \DateTime('now'));
                        $objectManager->flush();
                        $updatedArticles[$item['contentId']] = $contentListItem->getContent();

                        break;
                    case 'add':
                        $object = $this->get('swp.repository.article')->findOneById($item['contentId']);
                        $contentListItem = $this->get('swp.service.content_list')
                            ->addArticleToContentList($list, $object, $item['position']);
                        $objectManager->persist($contentListItem);
                        $list->setUpdatedAt(new \DateTime('now'));
                        $objectManager->flush();
                        $updatedArticles[$item['contentId']] = $contentListItem->getContent();

                        break;
                    case 'delete':
                        $contentListItem = $this->findByContentOr404($list, $item['contentId']);
                        $objectManager->remove($contentListItem);
                        $list->setUpdatedAt(new \DateTime('now'));
                        $objectManager->flush();
                        $updatedArticles[$item['contentId']] = $contentListItem->getContent();

                        break;
                }
            }

            foreach ($updatedArticles as $updatedArticle) {
                $this->get('event_dispatcher')->dispatch(ArticleEvents::POST_UPDATE, new ArticleEvent(
                    $updatedArticle,
                    $updatedArticle->getPackage(),
                    ArticleEvents::POST_UPDATE
                ));
            }

            return new SingleResourceResponse($list, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findByContentOr404($listId, $contentId): ContentListItemInterface
    {
        $listItem = $this->get('swp.repository.content_list_item')->findOneBy([
            'contentList' => $listId,
            'content' => $contentId,
        ]);

        if (null === $listItem) {
            throw new NotFoundHttpException(sprintf('Content list item with content_id "%s" was not found on that list. If You want to add new item - use action type "add".', $contentId));
        }

        return $listItem;
    }

    private function findOr404($listId, $id): ContentListItemInterface
    {
        $listItem = $this->get('swp.repository.content_list_item')->findOneBy([
            'contentList' => $listId,
            'id' => $id,
        ]);

        if (null === $listItem) {
            throw new NotFoundHttpException(sprintf('Content list item with id "%s" was not found.', $id));
        }

        return $listItem;
    }
}
