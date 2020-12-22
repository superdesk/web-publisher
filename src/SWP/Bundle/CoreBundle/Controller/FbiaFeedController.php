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

use SWP\Bundle\CoreBundle\Form\Type\FacebookInstantArticlesFeedType;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FbiaFeedController extends AbstractController
{
    /**
     * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_instant_articles_feed")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $repository = $this->get('swp.repository.facebook_instant_articles_feed');

        $items = $repository->getPaginatedByCriteria(
            new Criteria(),
            $request->query->get('sorting', ['createdAt' => 'desc']),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    /**
     * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_create_facebook_instant_articles_feed")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        /* @var FacebookInstantArticlesFeedInterface $feed */
        $feed = $this->get('swp.factory.facebook_instant_articles_feed')->create();
        $form = $form = $this->get('form.factory')->createNamed('', FacebookInstantArticlesFeedType::class, $feed, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->checkIfFeedExists($feed->getContentBucket(), $feed->getFacebookPage());
            $this->get('swp.repository.facebook_instant_articles_feed')->add($feed);

            return new SingleResourceResponse($feed, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function checkIfFeedExists($contentBucket, $facebookPage)
    {
        if (null !== $this->get('swp.repository.facebook_instant_articles_feed')->findOneBy([
                'contentBucket' => $contentBucket,
                'facebookPage' => $facebookPage,
            ])) {
            throw new ConflictHttpException('Feed for that page and content bucket already exists!');
        }
    }
}
