<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class ArticleSourceController extends AbstractController {

  private EntityRepository $entityRepository;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param EntityRepository $entityRepository
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(EntityRepository $entityRepository, EventDispatcherInterface $eventDispatcher) {
    $this->entityRepository = $entityRepository;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * @Route("/api/{version}/content/sources/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_article_sources")
   */
  public function listAction(Request $request): ResourcesListResponseInterface {
    $sorting = $request->query->all('sorting');
    $lists = $this->entityRepository->getPaginatedByCriteria(
        $this->eventDispatcher,
        new Criteria(),
        $sorting,
        new PaginationData($request));

    return new ResourcesListResponse($lists);
  }
}
