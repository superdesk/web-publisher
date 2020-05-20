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

use Doctrine\Common\Cache\Cache;
use Hoa\Mime\Mime;
use League\Flysystem\Filesystem;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\AnalyticsExport\CsvReportFileLocationResolver;
use SWP\Bundle\CoreBundle\AnalyticsExport\ExportAnalytics;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Form\Type\ExportAnalyticsType;
use SWP\Bundle\CoreBundle\Model\AnalyticsReport;
use SWP\Bundle\CoreBundle\Model\AnalyticsReportInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Model\DateTime as PublisherDateTime;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsExportController extends AbstractController
{
    /** @var Cache */
    protected $cacheProvider;

    /** @var RepositoryInterface */
    protected $analyticsReportRepository;

    /** @var Filesystem */
    protected $filesystem;

    /** @var CsvReportFileLocationResolver */
    protected $csvReportFileLocationResolver;

    /** @var CachedTenantContextInterface */
    protected $cachedTenantContext;

    /** @var RouteRepositoryInterface */
    protected $routeRepository;

    public function __construct(
        Cache $cacheProvider,
        RepositoryInterface $analyticsReportRepository,
        Filesystem $filesystem,
        CsvReportFileLocationResolver $csvReportFileLocationResolver,
        CachedTenantContextInterface $cachedTenantContext,
        RouteRepositoryInterface $routeRepository
    ) {
        $this->cacheProvider = $cacheProvider;
        $this->analyticsReportRepository = $analyticsReportRepository;
        $this->filesystem = $filesystem;
        $this->csvReportFileLocationResolver = $csvReportFileLocationResolver;
        $this->cachedTenantContext = $cachedTenantContext;
        $this->routeRepository = $routeRepository;
    }

    /**
     * @Route("/api/{version}/export/analytics/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_analytics_export_post")
     *
     * @throws \Exception
     */
    public function post(Request $request): SingleResourceResponseInterface
    {
        /** @var UserInterface $currentlyLoggedInUser */
        $currentlyLoggedInUser = $this->getUser();

        $now = PublisherDateTime::getCurrentDateTime();
        $fileName = 'analytics-'.$now->format('Y-m-d-H:i:s').'.csv';

        $form = $this->get('form.factory')->createNamed('', ExportAnalyticsType::class, null, ['method' => $request->getMethod()]);
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
                !empty($data['routes']) ? $this->processRoutesToIds($data['routes'][0]) : [],
                $data['authors'],
                $data['term'] ?? ''
            );

            $filters = $this->processFilters($exportAnalytics->getFilters(), !empty($data['routes']) ? $data['routes'][0] : []);
            $analyticsReport->setFilters($filters);

            $this->analyticsReportRepository->add($analyticsReport);

            $this->dispatchMessage($exportAnalytics);

            return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/export/analytics/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_analytics_reports")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $redirectRoutes = $this->analyticsReportRepository->getPaginatedByCriteria(
            new Criteria(),
            $request->query->get('sorting', []),
            new PaginationData($request)
        );

        return new ResourcesListResponse($redirectRoutes);
    }

    /**
     * @Route("/analytics/export/{fileName}", methods={"GET"}, options={"expose"=true}, requirements={"mediaId"=".+"}, name="swp_export_analytics_download")
     */
    public function downloadFile(string $fileName): Response
    {
        $cacheKey = md5(serialize(['analytics_report', $fileName]));
        if (!$this->cacheProvider->contains($cacheKey)) {
            /** @var AnalyticsReportInterface $analyticsReport */
            $analyticsReport = $this->analyticsReportRepository->findOneBy(['assetId' => $fileName]);
            $this->cacheProvider->save($cacheKey, $analyticsReport, 63072000);
        } else {
            $analyticsReport = $this->cacheProvider->fetch($cacheKey);
        }

        if (null === $analyticsReport) {
            throw new NotFoundHttpException('Report file was not found.');
        }

        $response = new Response();
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            str_replace('/', '_', $fileName)
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', Mime::getMimeFromExtension($analyticsReport->getFileExtension()));

        $response->setPublic();
        $response->setMaxAge(63072000);
        $response->setSharedMaxAge(63072000);
        $response->setLastModified($analyticsReport->getUpdatedAt() ?: $analyticsReport->getCreatedAt());
        $response->setContent($this->filesystem->read($this->csvReportFileLocationResolver->getMediaBasePath().'/'.$analyticsReport->getAssetId()));

        return $response;
    }

    private function processRoutesToIds(array $routes): array
    {
        $routeIds = [];

        foreach ($routes as $route) {
            $routeIds[] = $route->getId();
        }

        return $routeIds;
    }

    private function processFilters(array $filters, array $routes): array
    {
        $routeNames = [];
        foreach ($routes as $route) {
            $routeNames[] = $route->getName();
        }

        $filters['routes'] = $routeNames;

        return $filters;
    }
}
