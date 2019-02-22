<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Manager;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Hoa\Mime\Mime;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\FileFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Flysystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MediaManager implements MediaManagerInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ArticleMediaRepositoryInterface
     */
    protected $mediaRepository;

    /**
     * @var FileFactoryInterface
     */
    protected $fileFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ArticleMediaRepositoryInterface $mediaRepository,
        Filesystem $filesystem,
        RouterInterface $router,
        FileFactoryInterface $fileFactory,
        LoggerInterface $logger
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->filesystem = $filesystem;
        $this->router = $router;
        $this->fileFactory = $fileFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId)
    {
        $mediaId = ArticleMedia::handleMediaId($mediaId);
        $asset = $this->createMediaAsset($uploadedFile, $mediaId);
        $this->saveFile($uploadedFile, $mediaId);
        $this->mediaRepository->persist($asset);

        return $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(FileInterface $media)
    {
        return $this->filesystem->read($this->getMediaBasePath().'/'.$media->getAssetId().'.'.$media->getFileExtension());
    }

    /**
     * {@inheritdoc}
     */
    public function saveFile(UploadedFile $uploadedFile, $fileName)
    {
        $extension = $this->guessExtension($uploadedFile);
        $filePath = $this->getMediaBasePath().'/'.$fileName.'.'.$extension;

        if ($this->filesystem->has($filePath)) {
            return true;
        }

        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $result = $this->filesystem->writeStream($filePath, $stream);
        fclose($stream);

        return $result;
    }

    public function downloadFile(string $url, string $mediaId, string $mimeType = null): UploadedFile
    {
        $pathParts = \pathinfo($url);
        if (null === $mimeType) {
            $mimeType = Mime::getMimeFromExtension($pathParts['extension']);
        }

        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        $client = new Client(array('handler' => $handlerStack));
        $tempLocation = \rtrim(\sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.\sha1($mediaId.date('his'));
        $client->request('GET', $url, ['sink' => $tempLocation]);

        return new UploadedFile($tempLocation, $mediaId, $mimeType, \strlen($tempLocation), null, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl(FileInterface $media)
    {
        return $this->getMediaUri($media, RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaUri(FileInterface $media, $type = RouterInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate('swp_media_get', [
            'mediaId' => $media->getAssetId(),
            'extension' => $media->getFileExtension(),
        ], $type);
    }

    /**
     * {@inheritdoc}
     */
    public function createMediaAsset(UploadedFile $uploadedFile, string $assetId): FileInterface
    {
        $extension = $this->guessExtension($uploadedFile);

        return $this->fileFactory->createWith($assetId, $extension);
    }

    /**
     * @return string
     */
    protected function getMediaBasePath(): string
    {
        $pathElements = ['swp', 'media'];

        return implode('/', $pathElements);
    }

    private function guessExtension(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->guessExtension();

        if ('mpga' === $extension && 'mp3' === $uploadedFile->getExtension()) {
            $extension = 'mp3';
        }

        return $extension;
    }

    public function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ): bool {
            $retry = false;
            if ($retries >= 4) {
                $this->logger->error(\sprintf('Maximum number of retires reached'));

                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                $retry = true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 400) {
                    $retry = true;
                }
            }

            if (true === $retry) {
                $this->logger->info(\sprintf('Retry downloading %s', $request->getUri()));
            }

            return $retry;
        };
    }

    public function retryDelay()
    {
        return function ($numberOfRetries): int {
            return 1000 * $numberOfRetries;
        };
    }
}
