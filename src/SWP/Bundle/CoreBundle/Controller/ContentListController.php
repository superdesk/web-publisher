<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Exception;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListType;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Service\AuthorHelper;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Request\RequestParser;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\ContentList\ContentListEvents;
use SWP\Component\ContentList\Model\ContentListInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContentListController extends Controller
{
    /**
     * @Route("/api/{version}/content/lists/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_content_list_lists")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $repository = $this->get('swp.repository.content_list');

        $lists = $repository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($lists);
    }

    /**
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_content_show_lists", requirements={"id"="\d+"})
     */
    public function getAction($id): SingleResourceResponseInterface
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * @Route("/api/{version}/content/lists/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_content_create_lists")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        /* @var ContentListInterface $contentList */
        $contentList = $this->get('swp.factory.content_list')->create();
        $form = $form = $this->get('form.factory')->createNamed('', ContentListType::class, $contentList, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->ensureContentListExists($contentList->getName());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swp.repository.content_list')->add($contentList);

            return new SingleResourceResponse($contentList, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_content_update_lists", requirements={"id"="\d+"})
     */
    public function updateAction(Request $request, int $id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.content_list');
        /** @var ContentListInterface $contentList */
        $contentList = $this->findOr404($id);
        $filters = $contentList->getFilters();
        $listLimit = $contentList->getLimit();

        $form = $form = $this->get('form.factory')->createNamed('', ContentListType::class, $contentList, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if (isset($filters['author'])) {
            $filters['author'] = AuthorHelper::authorsToIds($filters['author']);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('event_dispatcher')->dispatch(
                ContentListEvents::LIST_CRITERIA_CHANGE,
                new GenericEvent($contentList, ['filters' => $filters, 'previousLimit' => $listLimit])
            );

            $objectManager->flush();

            return new SingleResourceResponse($contentList);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_content_delete_lists", requirements={"id"="\d+"})
     */
    public function deleteAction($id): SingleResourceResponseInterface
    {
        $repository = $this->get('swp.repository.content_list');
        $contentList = $this->findOr404($id);

        $repository->remove($contentList);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @Route("/api/{version}/content/lists/{id}", requirements={"id"="\w+"}, defaults={"version"="v2"}, methods={"LINK","UNLINK"}, name="swp_api_content_list_link_unlink")
     */
    public function linkUnlinkToContentListAction(Request $request, string $id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.content_list');
        /** @var ContentListInterface $contentList */
        $contentList = $this->findOr404($id);

        $matched = false;
        foreach ($request->attributes->get('links', []) as $key => $objectArray) {
            if (!is_array($objectArray)) {
                continue;
            }

            $object = $objectArray['object'];
            if ($object instanceof Exception) {
                throw $object;
            }

            if ($object instanceof ArticleInterface) {
                $contentListItem = $this->get('swp.repository.content_list_item')
                    ->findOneBy([
                        'contentList' => $contentList,
                        'content' => $object,
                    ]);

                if ('LINK' === $request->getMethod()) {
                    $position = 0;
                    if (count($notConvertedLinks = RequestParser::getNotConvertedLinks($request->attributes->get('links'))) > 0) {
                        foreach ($notConvertedLinks as $link) {
                            if (isset($link['resourceType']) && 'position' === $link['resourceType']) {
                                $position = $link['resource'];
                            }
                        }
                    }

                    if (false === $position && $contentListItem) {
                        throw new ConflictHttpException('This content is already linked to Content List');
                    }

                    if (!$contentListItem) {
                        $contentListItem = $this->get('swp.service.content_list')->addArticleToContentList($contentList, $object, $position);
                        $objectManager->persist($contentListItem);
                    } else {
                        $contentListItem->setPosition($position);
                    }

                    $objectManager->flush();
                } elseif ('UNLINK' === $request->getMethod()) {
                    if ($contentListItem->getContentList() !== $contentList) {
                        throw new ConflictHttpException('Content is not linked to content list');
                    }
                    $objectManager->remove($contentListItem);
                }

                $matched = true;

                break;
            }
        }
        if (false === $matched) {
            throw new NotFoundHttpException('Any supported link object was not found');
        }

        $objectManager->flush();

        return new SingleResourceResponse($contentList, new ResponseContext(201));
    }

    private function findOr404($id)
    {
        if (null === $list = $this->get('swp.repository.content_list')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Content list with id "%s" was not found.', $id));
        }

        return $list;
    }

    private function ensureContentListExists($name)
    {
        if (null !== $this->get('swp.repository.content_list')->findOneByName($name)) {
            throw new ConflictHttpException(sprintf('Content list named "%s" already exists!', $name));
        }
    }
}
