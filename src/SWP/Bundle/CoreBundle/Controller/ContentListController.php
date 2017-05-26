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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListType;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Request\RequestParser;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\ContentList\ContentListEvents;
use SWP\Component\ContentList\Model\ContentListInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentListController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all content lists",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/content/lists/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_lists")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.content_list');

        $lists = $repository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($lists);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Show single content list",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_show_lists", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Create new content list",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when not valid data."
     *     },
     *     input="SWP\Bundle\ContentListBundle\Form\Type\ContentListType"
     * )
     * @Route("/api/{version}/content/lists/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_create_lists")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        /* @var ContentListInterface $contentList */
        $contentList = $this->get('swp.factory.content_list')->create();
        $form = $this->createForm(ContentListType::class, $contentList, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->ensureContentListExists($contentList->getName());

        if ($form->isValid()) {
            $this->get('swp.repository.content_list')->add($contentList);

            return new SingleResourceResponse($contentList, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Update single content list",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when not valid data.",
     *         404="Returned when not found.",
     *         409="Returned on conflict."
     *     },
     *     input="SWP\Bundle\ContentListBundle\Form\Type\ContentListType"
     * )
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_update_lists", requirements={"id"="\d+"})
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $objectManager = $this->get('swp.object_manager.content_list');
        /** @var ContentListInterface $contentList */
        $contentList = $this->findOr404($id);
        $filters = $contentList->getFilters();

        $form = $this->createForm(ContentListType::class, $contentList, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(
                ContentListEvents::LIST_CRITERIA_CHANGE,
                new GenericEvent($contentList, ['filters' => $filters])
            );

            $objectManager->flush();

            return new SingleResourceResponse($contentList);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single content list",
     *     statusCodes={
     *         204="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/lists/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_delete_lists", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $repository = $this->get('swp.repository.content_list');
        $contentList = $this->findOr404($id);

        $repository->remove($contentList);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * Link or Unlink resource with Content List.
     *
     * **link or unlink content**:
     *
     *     header name: "link"
     *     header value: "</api/{version}/content/articles/{id}; rel="article">"
     *
     * or with specific position:
     *
     *     header name: "link"
     *     header value: "</api/{version}/content/articles/{id}; rel="article">,<1; rel="position">"
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found",
     *         409={
     *           "Returned when the link already exists",
     *         }
     *     }
     * )
     *
     * @Route("/api/{version}/content/lists/{id}", requirements={"id"="\w+"}, defaults={"version"="v1"}, name="swp_api_content_list_link_unlink")
     *
     * @Method("LINK|UNLINK")
     *
     * @param Request $request
     * @param string  $id
     *
     * @throws NotFoundHttpException
     * @throws \Exception
     *
     * @return SingleResourceResponse
     */
    public function linkUnlinkToContentListAction(Request $request, $id)
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
            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof ArticleInterface) {
                $contentListItem = $this->get('swp.repository.content_list_item')
                    ->findOneBy([
                        'contentList' => $contentList,
                        'content' => $object,
                    ]);

                if ($request->getMethod() === 'LINK') {
                    $position = 0;
                    if (count($notConvertedLinks = RequestParser::getNotConvertedLinks($request->attributes->get('links'))) > 0) {
                        foreach ($notConvertedLinks as $link) {
                            if (isset($link['resourceType']) && $link['resourceType'] == 'position') {
                                $position = $link['resource'];
                            }
                        }
                    }

                    if ($position === false && $contentListItem) {
                        throw new ConflictHttpException('This content is already linked to Content List');
                    }

                    if (!$contentListItem) {
                        $contentListItem = $this->get('swp.service.content_list')->addArticleToContentList($contentList, $object, $position);
                        $objectManager->persist($contentListItem);
                    } else {
                        $contentListItem->setPosition($position);
                    }

                    $objectManager->flush();
                } elseif ($request->getMethod() === 'UNLINK') {
                    if (!$contentList->getItems()->contains($contentListItem)) {
                        throw new ConflictHttpException('Content is not linked to content list');
                    }
                    $objectManager->remove($contentListItem);
                }

                $matched = true;
                break;
            }
        }
        if ($matched === false) {
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
