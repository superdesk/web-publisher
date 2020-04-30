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

use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListItemsType;
use SWP\Bundle\ContentListBundle\Services\ContentListServiceInterface;
use SWP\Bundle\CoreBundle\Form\Type\ContentListItemType;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContentListItemController extends AbstractController
{
    private $contentListItemRepository;

    private $entityManager;

    private $contentListService;

    public function __construct(
        ContentListItemRepositoryInterface $contentListItemRepository,
        EntityManagerInterface $entityManager,
        ContentListServiceInterface $contentListService
    ) {
        $this->contentListItemRepository = $contentListItemRepository;
        $this->entityManager = $entityManager;
        $this->contentListService = $contentListService;
    }

    /**
     * List all items of content list.
     *
     * @Operation(
     *     tags={"content list"},
     *     summary="Lists content list items",
     *     @SWG\Parameter(
     *         name="sticky",
     *         in="query",
     *         description="Keep item on top of the list",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [updatedAt]=asc|desc",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\ContentListItem::class, groups={"api"}))
     *         )
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
    public function listAction(Request $request, int $id): ResourcesListResponseInterface
    {
        $items = $this->contentListItemRepository->getPaginatedByCriteria(
            new Criteria([
                'contentList' => $id,
                'sticky' => $request->query->get('sticky', ''),
            ]),
            $request->query->get('sorting', ['sticky' => 'desc']),
            new PaginationData($request)
        );

        $responseContext = new ResponseContext();
        $responseContext->setSerializationGroups(
            [
                'Default',
                'api_packages_list',
                'api_content_list_item_details',
                'api_articles_list',
                'api_articles_featuremedia',
                'api_article_authors',
                'api_article_media_list',
                'api_article_media_renditions',
                'api_articles_statistics_list',
                'api_image_details',
                'api_routes_list',
                'api_tenant_list',
            ]
        );

        return new ResourcesListResponse($items, $responseContext);
    }

    /**
     * @Operation(
     *     tags={"content list"},
     *     summary="Get single content list item",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\ContentListItem::class, groups={"api"})
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
     *     tags={"content list"},
     *     summary="Update single content list item",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=ContentListItemType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\ContentListItem::class, groups={"api"})
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
    public function updateAction(Request $request, FormFactoryInterface $formFactory, $listId, $id): SingleResourceResponseInterface
    {
        $contentListItem = $this->findOr404($listId, $id);
        $form = $formFactory->createNamed(
            '',
            ContentListItemType::class,
            $contentListItem,
            ['method' => $request->getMethod()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contentListItem->getContentList()->setUpdatedAt(new DateTime());

            $contentListItem->setPosition($contentListItem->getStickyPosition());

            $this->entityManager->flush();

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
     *     tags={"content list"},
     *     summary="Update many content list items",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=ContentListItemsType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\ContentList::class, groups={"api"})
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
    public function batchUpdateAction(
        Request $request,
        FormFactoryInterface $formFactory,
        ContentListRepositoryInterface $contentListRepository,
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        int $listId
    ): SingleResourceResponseInterface {
        /** @var ContentListInterface $list */
        $list = $contentListRepository->findOneBy(['id' => $listId]);
        if (null === $list) {
            throw new NotFoundHttpException(sprintf('Content list with id "%s" was not found.', $list));
        }

        $form = $formFactory->createNamed('', ContentListItemsType::class, [], ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $updatedAt = DateTime::createFromFormat(DateTime::RFC3339, $data['updatedAt'], new DateTimeZone('UTC'));
            $updatedAt->setTimezone(new DateTimeZone('UTC'));
            $listUpdatedAt = $list->getUpdatedAt();
            $listUpdatedAt->setTimezone(new DateTimeZone('UTC'));
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

                        $this->ensureThereIsNoItemOnPositionOrThrow409($listId, $item['position'], $item['sticky']);

                        $contentListItem->setPosition($item['position']);
                        $this->contentListService->toggleStickOnItemPosition($contentListItem, $item['sticky'], $item['position']);

                        $list->setUpdatedAt(new DateTime('now'));
                        $this->entityManager->flush();
                        $updatedArticles[$item['contentId']] = $contentListItem->getContent();

                        break;
                    case 'add':
                        $this->ensureThereIsNoItemOnPositionOrThrow409($listId, $item['position'], $item['sticky']);

                        $object = $articleRepository->findOneById($item['contentId']);
                        $contentListItem = $this->contentListService->addArticleToContentList($list, $object, $item['position'], $item['sticky']);

                        $updatedArticles[$item['contentId']] = $contentListItem->getContent();

                        break;
                    case 'delete':
                        $contentListItem = $this->findByContentOr404($list, $item['contentId']);
                        $this->entityManager->remove($contentListItem);
                        $list->setUpdatedAt(new DateTime('now'));
                        $this->entityManager->flush();
                        $updatedArticles[$item['contentId']] = $contentListItem->getContent();

                        break;
                }
            }

            $this->contentListService->repositionStickyItems($list);

            foreach ($updatedArticles as $updatedArticle) {
                $eventDispatcher->dispatch(ArticleEvents::POST_UPDATE, new ArticleEvent(
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
        /** @var ContentListItemInterface $listItem */
        $listItem = $this->contentListItemRepository->findOneBy([
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
        /** @var ContentListItemInterface $listItem */
        $listItem = $this->contentListItemRepository->findOneBy([
            'contentList' => $listId,
            'id' => $id,
        ]);

        if (null === $listItem) {
            throw new NotFoundHttpException(sprintf('Content list item with id "%s" was not found.', $id));
        }

        return $listItem;
    }

    private function ensureThereIsNoItemOnPositionOrThrow409(int $listId, int $position, bool $isSticky): void
    {
        $existingContentListItem = $this->contentListService->isAnyItemPinnedOnPosition($listId, $position);

        if (null !== $existingContentListItem && $isSticky && $existingContentListItem->isSticky()) {
            throw new ConflictHttpException('There is already an item pinned on that position. Unpin it first.');
        }
    }
}
