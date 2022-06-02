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

use SWP\Bundle\CoreBundle\Form\Type\FacebookPageType;
use SWP\Bundle\CoreBundle\Model\FacebookPage;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FbPageController extends AbstractController {

  private FormFactoryInterface $formFactory;
  private RepositoryInterface $facebookInstantArticlesFeedRepository;
  private RepositoryInterface $facebookPageRepository;
  private FactoryInterface $facebookPageFactory;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param FormFactoryInterface $formFactory
   * @param RepositoryInterface $facebookInstantArticlesFeedRepository
   * @param RepositoryInterface $facebookPageRepository
   * @param FactoryInterface $facebookPageFactory
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(FormFactoryInterface $formFactory,
                              RepositoryInterface  $facebookInstantArticlesFeedRepository,
                              RepositoryInterface  $facebookPageRepository, FactoryInterface $facebookPageFactory,
                              EventDispatcherInterface      $eventDispatcher) {
    $this->formFactory = $formFactory;
    $this->facebookInstantArticlesFeedRepository = $facebookInstantArticlesFeedRepository;
    $this->facebookPageRepository = $facebookPageRepository;
    $this->facebookPageFactory = $facebookPageFactory;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/facebook/pages/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_pages")
   */
  public function listAction(Request $request) {
    $repository = $this->facebookPageRepository;
    $sort = $request->query->all('sorting');
    if (empty($sort)) {
      $sort = ['id' => 'asc'];
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
   * @Route("/api/{version}/facebook/pages/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_create_facebook_pages")
   */
  public function createAction(Request $request) {
    /* @var FacebookPage $feed */
    $page = $this->facebookPageFactory->create();
    $form = $this->formFactory->createNamed('', FacebookPageType::class, $page, ['method' => $request->getMethod()]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $this->checkIfPageExists($page);
      $this->facebookPageRepository->add($page);

      return new SingleResourceResponse($page, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/facebook/pages/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_delete_facebook_pages")
   */
  public function deleteAction(int $id): SingleResourceResponseInterface {
    $repository = $this->facebookPageRepository;
    if (null === $page = $this->facebookPageRepository->findOneBy(['id' => $id])) {
      throw new NotFoundHttpException('There is no Page with provided id!');
    }

    if (null !== $feed = $this->facebookInstantArticlesFeedRepository->findOneBy(['facebookPage' => $id])) {
      throw new ConflictHttpException(sprintf('This Page is used by Instant Articles Feed with id: %s!', $feed->getId()));
    }

    $repository->remove($page);

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  private function checkIfPageExists(PageInterface $page): void {
    if (null !== $this->facebookPageRepository->findOneBy([
            'pageId' => $page->getPageId(),
        ])
    ) {
      throw new ConflictHttpException('This Page already exists!');
    }
  }
}
