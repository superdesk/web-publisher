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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @ApiDoc(
     *     resource=true,
     *     description="Lists content list items",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Content list item not found.",
     *         500="Unexpected error."
     *     },
     *     filters={
     *         {"name"="sticky", "dataType"="boolean", "pattern"="true|false"},
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/content/lists/{id}/items/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_items", requirements={"id"="\d+"})
     * @Method("GET")
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
     * @ApiDoc(
     *     resource=true,
     *     description="Get single content list item",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/lists/{listId}/items/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_show_lists_item", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($listId, $id)
    {
        return new SingleResourceResponse($this->findOr404($listId, $id));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Update single content list item",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when not valid data.",
     *         404="Returned when not found."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\ContentListItemType"
     * )
     * @Route("/api/{version}/content/lists/{listId}/items/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_lists_item", requirements={"id"="\d+", "listId"="\d+"})
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $listId, $id)
    {
        $objectManager = $this->get('swp.object_manager.content_list_item');
        $contentListItem = $this->findOr404($listId, $id);
        $form = $this->createForm(
            ContentListItemType::class,
            $contentListItem,
            ['method' => $request->getMethod()]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
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
     * @ApiDoc(
     *     resource=true,
     *     description="Update many content list items",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when not valid data.",
     *         404="Returned when not found."
     *     },
     *     input="SWP\Bundle\ContentListBundle\Form\Type\ContentListItemsType"
     * )
     * @Route("/api/{version}/content/lists/{listId}/items/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_batch_update_lists_item", requirements={"listId"="\d+"})
     * @Method("PATCH")
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
        $form = $this->createForm(ContentListItemsType::class, [], ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $updatedAt = \DateTime::createFromFormat(\DateTime::RFC3339, $data['updated_at'], new \DateTimeZone('UTC'));
            $updatedAt->setTimezone(new \DateTimeZone('UTC'));
            if ($updatedAt != $list->getUpdatedAt()) {
                throw new ConflictHttpException('List was already updated');
            }

            foreach ($data['items'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (!isset($item['position']) || !is_numeric($item['position'])) {
                    $item['position'] = 0;
                }

                switch ($item['action']) {
                    case 'move':
                        /** @var ContentListItemInterface $contentListItem */
                        $contentListItem = $this->findByContentOr404($list, $item['content_id']);
                        $contentListItem->setPosition($item['position']);
                        $list->setUpdatedAt(new \DateTime('now'));
                        $objectManager->flush();
                        break;
                    case 'add':
                        $object = $this->get('swp.repository.article')->findOneById($item['content_id']);
                        $contentListItem = $this->get('swp.service.content_list')
                            ->addArticleToContentList($list, $object, $item['position']);
                        $objectManager->persist($contentListItem);
                        $list->setUpdatedAt(new \DateTime('now'));
                        $objectManager->flush();
                        break;
                    case 'delete':
                        $contentListItem = $this->findByContentOr404($list, $item['content_id']);
                        $objectManager->remove($contentListItem);
                        $list->setUpdatedAt(new \DateTime('now'));
                        $objectManager->flush();
                        break;
                }
            }

            return new SingleResourceResponse($list, new ResponseContext(201));
        }
    }

    private function findByContentOr404($listId, $contentId)
    {
        $listItem = $this->get('swp.repository.content_list_item')->findOneBy([
            'contentList' => $listId,
            'content' => $contentId,
        ]);

        if (null === $listItem) {
            throw new NotFoundHttpException(sprintf('Content list item with content_id "%s" was not found.', $contentId));
        }

        return $listItem;
    }

    private function findOr404($listId, $id)
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
