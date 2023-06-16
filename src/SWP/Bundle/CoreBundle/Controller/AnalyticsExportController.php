<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Behat\Testwork\Output\ServiceContainer\Formatter\FormatterFactory;
use League\Flysystem\Filesystem;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\AnalyticsExport\CsvReportFileLocationResolver;
use SWP\Bundle\CoreBundle\AnalyticsExport\ExportAnalytics;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Form\Type\ExportAnalyticsType;
use SWP\Bundle\CoreBundle\Model\AnalyticsReport;
use SWP\Bundle\CoreBundle\Model\AnalyticsReportInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\CoreBundle\Util\MimeTypeHelper;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Model\DateTime as PublisherDateTime;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use FOS\RestBundle\Controller\Annotations\Route as FosRoute;

class AnalyticsExportController extends AbstractController {

  protected CacheInterface $cacheProvider;
  protected RepositoryInterface $analyticsReportRepository;
  protected Filesystem $filesystem;
  protected CsvReportFileLocationResolver $csvReportFileLocationResolver;
  protected CachedTenantContextInterface $cachedTenantContext;
  protected RouteRepositoryInterface $routeRepository;
  private MessageBusInterface $messageBus;
  private FormFactoryInterface $formFactory;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param CacheInterface $cacheProvider
   * @param RepositoryInterface $analyticsReportRepository
   * @param Filesystem $filesystem
   * @param CsvReportFileLocationResolver $csvReportFileLocationResolver
   * @param CachedTenantContextInterface $cachedTenantContext
   * @param RouteRepositoryInterface $routeRepository
   * @param MessageBusInterface $messageBus
   * @param FormFactoryInterface $formFactory
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(CacheInterface                $cacheProvider,
                              RepositoryInterface           $analyticsReportRepository, Filesystem $filesystem,
                              CsvReportFileLocationResolver $csvReportFileLocationResolver,
                              CachedTenantContextInterface  $cachedTenantContext,
                              RouteRepositoryInterface      $routeRepository, MessageBusInterface $messageBus,
                              FormFactoryInterface          $formFactory, EventDispatcherInterface $eventDispatcher) {
    $this->cacheProvider = $cacheProvider;
    $this->analyticsReportRepository = $analyticsReportRepository;
    $this->filesystem = $filesystem;
    $this->csvReportFileLocationResolver = $csvReportFileLocationResolver;
    $this->cachedTenantContext = $cachedTenantContext;
    $this->routeRepository = $routeRepository;
    $this->messageBus = $messageBus;
    $this->formFactory = $formFactory;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @FosRoute("/api/{version}/export/analytics/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_analytics_export_post")
   *
   * @throws \Exception
   */
  public function post(Request $request): SingleResourceResponseInterface {
    /** @var UserInterface $currentlyLoggedInUser */
    $currentlyLoggedInUser = $this->getUser();

    $now = PublisherDateTime::getCurrentDateTime();
    $fileName = 'analytics-' . $now->format('Y-m-d-H:i:s') . '.csv';

    $form = $this->formFactory->createNamed('', ExportAnalyticsType::class, null, ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->getData();

      $analyticsReport = new AnalyticsReport();
      $analyticsReport->setAssetId($fileName);
      $analyticsReport->setFileExtension('csv');
      $analyticsReport->setUser($currentlyLoggedInUser);

      $exportAnalytics = new ExportAnalytics(
          $data['start'],
          $data['end'],
          $this->cachedTenantContext->getTenant()->getCode(),
          $fileName,
          $currentlyLoggedInUser->getEmail(),
          !empty($data['routes']) ? $this->toIds($data['routes']) : [],
          !empty($data['authors']) ? $this->toIds($data['authors']) : [],
          $data['term'] ?? ''
      );

      $filters = $this->processFilters(
          $exportAnalytics->getFilters(),
          !empty($data['routes']) ? $data['routes'] : [],
          !empty($data['authors']) ? $data['authors'] : []
      );

      $analyticsReport->setFilters($filters);

      $this->analyticsReportRepository->add($analyticsReport);

      $this->messageBus->dispatch($exportAnalytics);

      return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @FosRoute("/api/{version}/export/analytics/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_analytics_reports")
   */
  public function listAction(Request $request): ResourcesListResponseInterface {
    $sorting = $request->query->all('sorting');
    $reports = $this->analyticsReportRepository->getPaginatedByCriteria(
        $this->eventDispatcher,
        new Criteria(),
        $sorting,
        new PaginationData($request)
    );

    return new ResourcesListResponse($reports);
  }

  /**
   * @Route("/analytics/export/{fileName}", methods={"GET"}, options={"expose"=true}, requirements={"mediaId"=".+"}, name="swp_export_analytics_download")
   */
  public function downloadFile(string $fileName): Response {
    $cacheKey = md5(serialize(['analytics_report', $fileName]));

    $analyticsReport = $this->cacheProvider->get($cacheKey, function () use ($fileName) {
      /* @var AnalyticsReportInterface|null $analyticsReport */
      return $this->analyticsReportRepository->findOneBy(['assetId' => $fileName]);
    });

    if (null === $analyticsReport) {
      throw new NotFoundHttpException('Report file was not found.');
    }

    $response = new Response();
    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        str_replace('/', '_', $fileName)
    );

    $response->headers->set('Content-Disposition', $disposition);
    $response->headers->set('Content-Type', MimeTypeHelper::getByExtension($analyticsReport->getFileExtension()));

    $response->setPublic();
    $response->setMaxAge(63072000);
    $response->setSharedMaxAge(63072000);
    $response->setLastModified($analyticsReport->getUpdatedAt() ?: $analyticsReport->getCreatedAt());
    $response->setContent($this->filesystem->read($this->csvReportFileLocationResolver->getMediaBasePath() . '/' . $analyticsReport->getAssetId()));

    return $response;
  }

  private function toIds(array $items): array {
    $ids = [];
    foreach ($items as $item) {
      foreach ($item as $entity) {
        if (!$entity instanceof PersistableInterface) {
          continue;
        }

        $ids[] = $entity->getId();
      }
    }

    return $ids;
  }

  private function processFilters(array $filters, array $routes, array $authors): array {
    $routeNames = [];

    foreach ($routes as $route) {
      foreach ($route as $entity) {
        $routeNames[] = $entity->getName();
      }
    }

    $filters['routes'] = $routeNames;

    $authorNames = [];
    /** @var ArticleAuthorInterface $author */
    foreach ($authors as $author) {
      foreach ($author as $entity) {
        $authorNames[] = $entity->getName();
      }
    }

    $filters['authors'] = $authorNames;

    return $filters;
  }
}
