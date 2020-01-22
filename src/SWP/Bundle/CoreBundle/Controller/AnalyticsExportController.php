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

use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use DateTime;
use Doctrine\Common\Cache\Cache;
use Hoa\Mime\Mime;
use League\Flysystem\Filesystem;
use SWP\Bundle\CoreBundle\AnalyticsExport\CsvReportFileLocationResolver;
use SWP\Bundle\CoreBundle\AnalyticsExport\ExportAnalytics;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Model\AnalyticsReport;
use SWP\Bundle\CoreBundle\Model\AnalyticsReportInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use SWP\Component\Common\Model\DateTime as PublisherDateTime;

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

    public function __construct(
        Cache $cacheProvider,
        RepositoryInterface $analyticsReportRepository,
        Filesystem $filesystem,
        CsvReportFileLocationResolver $csvReportFileLocationResolver,
        CachedTenantContextInterface $cachedTenantContext
    ) {
        $this->cacheProvider = $cacheProvider;
        $this->analyticsReportRepository = $analyticsReportRepository;
        $this->filesystem = $filesystem;
        $this->csvReportFileLocationResolver = $csvReportFileLocationResolver;
        $this->cachedTenantContext = $cachedTenantContext;
    }

    /**
     * @Operation(
     *     tags={"export"},
     *     summary="Export analytics data",
     *     @SWG\Parameter(
     *         name="start",
     *         in="query",
     *         description="Export start date, e.g. 20150101",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="end",
     *         in="query",
     *         description="Export end date, e.g. 20160101",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="author",
     *         in="query",
     *         description="Authors names",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="route",
     *         in="query",
     *         description="Routes ids",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/export/analytics", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_analytics_export_post")
     *
     * @return SingleResourceResponse
     *
     * @throws \Exception
     */
    public function post(Request $request): SingleResourceResponse
    {
        /** @var UserInterface $currentlyLoggedInUser */
        $currentlyLoggedInUser = $this->getUser();

        $start = new DateTime($request->query->get('start', 'now'));
        $end = new DateTime($request->query->get('end', '-30 days'));
        $tenantCode = $this->cachedTenantContext->getTenant()->getCode();
        $userEmail = $currentlyLoggedInUser->getEmail();
        $routeIds = (array) $request->query->get('route', []);
        $authors = (array) $request->query->get('author', []);
        $now = PublisherDateTime::getCurrentDateTime();
        $fileName = 'analytics-'.$now->format('Y-m-d-H:i:s').'.csv';

        $analyticsReport = new AnalyticsReport();
        $analyticsReport->setAssetId($fileName);
        $analyticsReport->setFileExtension('csv');
        $analyticsReport->setUser($currentlyLoggedInUser);
        $this->analyticsReportRepository->add($analyticsReport);

        $this->dispatchMessage(new ExportAnalytics(
            $start,
            $end,
            $tenantCode,
            $fileName,
            $userEmail,
            $routeIds,
            $authors
        ));

        return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
    }

    /**
     * @Operation(
     *     tags={"export"},
     *     summary="Lists analytics reports",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [createdAt]=asc|desc",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreRoute\Model\AnalyticsReport::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/export/analytics", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_analytics_reports")
     */
    public function listAction(Request $request)
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
}
