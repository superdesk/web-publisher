<?php

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

use DateTime;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use SWP\Bundle\CoreBundle\Model\AnalyticsReport;
use SWP\Bundle\CoreBundle\Model\AnalyticsReportInterface;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

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

    public function __construct(
        RepositoryManagerInterface $elasticaRepositoryManager,
        string $cacheDir,
        ReportMailer $mailer,
        ReportFileUploader $reportFileUploader,
        CsvFileWriter $csvFileWriter,
        RepositoryInterface $analyticsReportRepository
    ) {
        $this->elasticaRepositoryManager = $elasticaRepositoryManager;
        $this->cacheDir = $cacheDir;
        $this->mailer = $mailer;
        $this->reportFileUploader = $reportFileUploader;
        $this->csvFileWriter = $csvFileWriter;
        $this->analyticsReportRepository = $analyticsReportRepository;
    }

    public function __invoke(ExportAnalytics $exportAnalytics)
    {
        $criteria = Criteria::fromQueryParameters(
            '',
            [
                'sort' => ['articleStatistics.pageViewsNumber' => 'desc'],
                'publishedBefore' => $exportAnalytics->getEnd(),
                'publishedAfter' => $exportAnalytics->getStart(),
                'tenantCode' => $exportAnalytics->getTenantCode(),
            ]
        );

        //$this->tenantContext->setTenant($this->packageObjectManager->findOneBy(Tenant::class, $exportAnalytics->getTenantCode())));

        $articleRepository = $this->elasticaRepositoryManager->getRepository(Article::class);

        $articles = $articleRepository->findByCriteria($criteria);
        $total = $articles->getTotalHits();
        $articles = $articles->getResults(0, $total);
        $data = $this->objectsToArray($articles->toArray());

        $fileName = $exportAnalytics->getFileName();
        $path = $this->cacheDir.'/'.$fileName;

        $this->csvFileWriter->write($path, $data);

        /** @var AnalyticsReportInterface $analyticsReport */
        $analyticsReport = $this->analyticsReportRepository->findOneBy(['assetId' => $fileName]);

        $url = $this->reportFileUploader->upload($analyticsReport, $path);

        $analyticsReport->setStatus(AnalyticsReportInterface::STATUS_COMPLETED);
        $analyticsReport->setUpdatedAt(new DateTime());

        $this->analyticsReportRepository->flush();

        $this->mailer->sendReportReadyEmailNotification($exportAnalytics->getUserEmail(), $url);
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
                $article->getArticleStatistics()->getPageViewsNumber(),
                $article->getRoute()->getName(),
                $article->getTitle(),
                implode(', ', $article->getAuthorsNames()),
            ];
        }

        return $data;
    }
}
