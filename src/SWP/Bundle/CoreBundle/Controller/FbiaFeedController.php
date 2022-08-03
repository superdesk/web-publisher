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
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use FOS\RestBundle\Controller\Annotations\Route;

class FbiaFeedController extends AbstractController {

  private FormFactoryInterface $formFactory;
  private RepositoryInterface $facebookInstantArticlesFeedRepository;
  private FactoryInterface $facebookInstantArticlesFeedFactory;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param FormFactoryInterface $formFactory
   * @param RepositoryInterface $facebookInstantArticlesFeedRepository
   * @param FactoryInterface $facebookInstantArticlesFeedFactory
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(FormFactoryInterface     $formFactory,
                              RepositoryInterface      $facebookInstantArticlesFeedRepository,
                              FactoryInterface         $facebookInstantArticlesFeedFactory,
                              EventDispatcherInterface $eventDispatcher) {
    $this->formFactory = $formFactory;
    $this->facebookInstantArticlesFeedRepository = $facebookInstantArticlesFeedRepository;
    $this->facebookInstantArticlesFeedFactory = $facebookInstantArticlesFeedFactory;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_instant_articles_feed")
   */
  public function listAction(Request $request): ResourcesListResponseInterface {
    $repository = $this->facebookInstantArticlesFeedRepository;
    $sort = $request->query->all('sorting');
    if (empty($sort)) {
      $sort = ['createdAt' => 'desc'];
    }

    $items = $repository->getPaginatedByCriteria(
        $this->eventDispatcher,
        new Criteria(),
        $sort,
        new PaginationData($request)
    );

    return new ResourcesListResponse($items);
  }

  /**
   * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_create_facebook_instant_articles_feed")
   */
  public function createAction(Request $request): SingleResourceResponseInterface {
    /* @var FacebookInstantArticlesFeedInterface $feed */
    $feed = $this->facebookInstantArticlesFeedFactory->create();
    $form = $this->formFactory->createNamed('', FacebookInstantArticlesFeedType::class, $feed, ['method' => $request->getMethod()]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $this->checkIfFeedExists($feed->getContentBucket(), $feed->getFacebookPage());
      $this->facebookInstantArticlesFeedRepository->add($feed);

      return new SingleResourceResponse($feed, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  private function checkIfFeedExists($contentBucket, $facebookPage) {
    if (null !== $this->facebookInstantArticlesFeedRepository->findOneBy([
            'contentBucket' => $contentBucket,
            'facebookPage' => $facebookPage,
        ])) {
      throw new ConflictHttpException('Feed for that page and content bucket already exists!');
    }
  }
}
