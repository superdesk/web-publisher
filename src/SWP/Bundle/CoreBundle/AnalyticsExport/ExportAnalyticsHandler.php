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

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use RuntimeException;
use SWP\Bundle\CoreBundle\AnalyticsExport\Exception\AnalyticsReportNotFoundException;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Model\AnalyticsReportInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Component\Common\Model\DateTime;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Throwable;

final class ExportAnalyticsHandler implements MessageHandlerInterface
{
    /** @var RepositoryManagerInterface */
    private $elasticaRepositoryManager;

    /** @var string */
    private $cacheDir;

    /** @var ReportMailer */
    private $mailer;

    /** @var ReportFileUploader */
    private $reportFileUploader;

    /** @var CsvFileWriter */
    private $csvFileWriter;

    /** @var RepositoryInterface */
    private $analyticsReportRepository;

    /** @var CachedTenantContextInterface */
    private $cachedTenantContext;

    /** @var TenantRepositoryInterface */
    private $tenantRepository;

    public function __construct(
        RepositoryManagerInterface $elasticaRepositoryManager,
        string $cacheDir,
        ReportMailer $mailer,
        ReportFileUploader $reportFileUploader,
        CsvFileWriter $csvFileWriter,
        RepositoryInterface $analyticsReportRepository,
        CachedTenantContextInterface $cachedTenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->elasticaRepositoryManager = $elasticaRepositoryManager;
        $this->cacheDir = $cacheDir;
        $this->mailer = $mailer;
        $this->reportFileUploader = $reportFileUploader;
        $this->csvFileWriter = $csvFileWriter;
        $this->analyticsReportRepository = $analyticsReportRepository;
        $this->cachedTenantContext = $cachedTenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    public function __invoke(ExportAnalytics $exportAnalytics)
    {
        $fileName = $exportAnalytics->getFileName();

        /** @var AnalyticsReportInterface $analyticsReport */
        $analyticsReport = $this->analyticsReportRepository->findOneBy(['assetId' => $fileName]);

        if (null === $analyticsReport) {
            throw new AnalyticsReportNotFoundException("Analytics report $fileName not found.");
        }

        try {
            $tenantCode = $exportAnalytics->getTenantCode();
            $criteria = Criteria::fromQueryParameters(
                $exportAnalytics->getTerm(),
                [
                    'sort' => ['articleStatistics.pageViewsNumber' => 'desc'],
                    'publishedBefore' => new \DateTime($exportAnalytics->getEnd()),
                    'publishedAfter' => new \DateTime($exportAnalytics->getStart()),
                    'tenantCode' => $tenantCode,
                    'routes' => $exportAnalytics->getRouteIds(),
                    'authors' => $exportAnalytics->getAuthors(),
                ]
            );

            $tenant = $this->tenantRepository->findOneBy(['code' => $tenantCode]);
            if (null === $tenant) {
                throw new RuntimeException("Tenant with code $tenantCode not found");
            }

            $this->cachedTenantContext->setTenant($tenant);

            $articleRepository = $this->elasticaRepositoryManager->getRepository(Article::class);

            $articles = $articleRepository->findByCriteria($criteria);
            $total = $articles->getTotalHits();
            $articles = $articles->getResults(0, 0 !== $total ? $total : 1);
            $data = $this->objectsToArray($articles->toArray());
            $path = $this->cacheDir.'/'.$fileName;

            $this->csvFileWriter->write($path, $data);

            $url = $this->reportFileUploader->upload($analyticsReport, $path);

            $this->mailer->sendReportReadyEmailNotification($exportAnalytics->getUserEmail(), $url);
            $analyticsReport->setStatus(AnalyticsReportInterface::STATUS_COMPLETED);
        } catch (Throwable $e) {
            $analyticsReport->setStatus(AnalyticsReportInterface::STATUS_ERRORED);
        }

        $analyticsReport->setUpdatedAt(DateTime::getCurrentDateTime());
        $this->analyticsReportRepository->flush();
    }

    private function objectsToArray(array $rows): array
    {
        $data = [
            ['Article ID', 'Publish Date', 'Total Views', 'Section', 'Title', 'Author(s)'],
        ];

        /** @var Article $article */
        foreach ($rows as $article) {
            $data[] = [
                $article->getId(),
                $article->getPublishedAt()->format('Y-m-d H:i'),
                null !== $article->getArticleStatistics() ? $article->getArticleStatistics()->getPageViewsNumber() : 0,
                $article->getRoute()->getName(),
                $article->getTitle(),
                implode(', ', $article->getAuthorsNames()),
            ];
        }

        return $data;
    }
}
