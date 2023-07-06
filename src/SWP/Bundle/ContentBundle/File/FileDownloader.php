<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\File;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use SWP\Bundle\CoreBundle\Util\MimeTypeHelper;
use function pathinfo;
use Psr\Log\LoggerInterface;
use function rtrim;
use function sha1;
use function sprintf;
use function strlen;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function sys_get_temp_dir;

final class FileDownloader implements FileDownloaderInterface
{
    private $logger;

    private $retryDownloads;

    public function __construct(LoggerInterface $logger, bool $retryDownloads)
    {
        $this->logger = $logger;
        $this->retryDownloads = $retryDownloads;
    }

    public function download(string $url, string $mediaId, string $mimeType = null): UploadedFile
    {
        $pathParts = pathinfo($url);
        if (null === $mimeType) {
            $mimeType = MimeTypeHelper::getByExtension($pathParts['extension']);
        }

        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        $client = new Client(['handler' => $handlerStack]);
        $tempLocation = rtrim(sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.sha1($mediaId.date('his'));
        $client->request('GET', $url, ['sink' => $tempLocation]);

        return new UploadedFile($tempLocation, $mediaId, $mimeType, strlen($tempLocation), true);
    }

    private function retryDecider(): callable
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ): bool {
            $retry = false;
            if (!$this->retryDownloads) {
                $this->logger->error(sprintf('Retries are disabled'));

                return false;
            }

            if ($retries >= 4) {
                $this->logger->error(sprintf('Maximum number of retires reached'));

                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException || ($response && $response->getStatusCode() >= 400)) {
                $retry = true;
            }

            if (true === $retry) {
                $this->logger->info(sprintf('Retry downloading %s', $request->getUri()));
            }

            return $retry;
        };
    }

    private function retryDelay(): callable
    {
        return static function ($numberOfRetries): int {
            return 1000 * $numberOfRetries;
        };
    }
}
