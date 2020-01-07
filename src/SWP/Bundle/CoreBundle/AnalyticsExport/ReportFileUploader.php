<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

use League\Flysystem\Filesystem;
use SWP\Bundle\CoreBundle\Model\AnalyticsReportInterface;
use Symfony\Component\Routing\RouterInterface;

class ReportFileUploader
{
    /** @var CsvReportFileLocationResolver */
    private $assetLocationResolver;

    /** @var Filesystem */
    private $filesystem;

    /** @var RouterInterface */
    private $router;

    public function __construct(
        CsvReportFileLocationResolver $assetLocationResolver,
        Filesystem $filesystem,
        RouterInterface $router
    ) {
        $this->assetLocationResolver = $assetLocationResolver;
        $this->filesystem = $filesystem;
        $this->router = $router;
    }

    public function upload(AnalyticsReportInterface $file, string $sourcePath): string
    {
        $uploadPath = $this->assetLocationResolver->getMediaBasePath().'/'.$file->getAssetId();

        $stream = fopen($sourcePath, 'rb+');
        $this->filesystem->writeStream($uploadPath, $stream);
        fclose($stream);

        return $this->router->generate('swp_export_analytics_download', [
            'fileName' => $file->getAssetId(),
        ], RouterInterface::ABSOLUTE_URL);
    }
}
