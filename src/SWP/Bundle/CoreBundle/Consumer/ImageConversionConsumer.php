<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Consumer;

use BadFunctionCallException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use function imagewebp;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\CoreBundle\Model\ImageInterface;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class ImageConversionConsumer implements ConsumerInterface
{
    protected $serializer;

    protected $logger;

    protected $imageRenditionRepository;

    protected $mediaManager;

    protected $tenantContext;

    protected $entityManager;

    public function __construct(
        SerializerInterface $serializer,
        LoggerInterface $logger,
        RepositoryInterface $imageRenditionRepository,
        MediaManagerInterface $mediaManager,
        TenantContextInterface $tenantContext,
        EntityManagerInterface $entityManager
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->imageRenditionRepository = $imageRenditionRepository;
        $this->mediaManager = $mediaManager;
        $this->tenantContext = $tenantContext;
        $this->entityManager = $entityManager;
    }

    public function execute(AMQPMessage $message): int
    {
        try {
            ['image' => $image, 'tenantId' => $tenantId] = unserialize($message->body, [false]);
            if (($tenant = $this->entityManager->find(Tenant::class, $tenantId)) instanceof TenantInterface) {
                $this->tenantContext->setTenant($tenant);
            }

            if (null === $image) {
                throw new InvalidArgumentException('Missing image data');
            }
        } catch (Throwable $e) {
            $this->logger->error('Message REJECTED: '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        }

        /** @var ImageInterface $image */
        $image = $this->entityManager->merge($image);
        $mediaId = $image->getAssetId();
        $tempLocation = rtrim(sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.sha1($mediaId);

        try {
            if (!function_exists('imagewebp')) {
                throw new BadFunctionCallException('"imagewebp" function is missing. Looks like GD was compiled without webp support');
            }
            $imageAsResource = $this->getImageAsResource($image);
            if (null === $imageAsResource) {
                throw new Exception('Could not get resource from provided images');
            }
            imagewebp($imageAsResource, $tempLocation);
            $uploadedFile = new UploadedFile($tempLocation, $mediaId, 'image/webp', strlen($tempLocation), null, true);
            $this->mediaManager->saveFile($uploadedFile, $mediaId);

            $this->logger->info(sprintf('File "%s" converted successfully to WEBP', $mediaId));

            $image->addVariant(ImageInterface::VARIANT_WEBP);
            $this->markArticlesMediaAsUpdated($image);

            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->logger->error('File NOT converted '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        } finally {
            $filesystem = new Filesystem();
            if ($filesystem->exists($tempLocation)) {
                $filesystem->remove($tempLocation);
            }
        }

        return ConsumerInterface::MSG_ACK;
    }

    private function markArticlesMediaAsUpdated($image)
    {
        /** @var ImageRenditionInterface[] $articleMedia */
        $articleMedia = $this->imageRenditionRepository->findBy(['image' => $image]);
        foreach ($articleMedia as $media) {
            $media->getMedia()->getArticle()->setMediaUpdatedAt(new DateTime());
        }
    }

    private function getImageAsResource(FileInterface $asset)
    {
        $filesystem = new Filesystem();
        $tempLocation = rtrim(sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.sha1($asset->getAssetId());
        $filesystem->dumpFile($tempLocation, $this->mediaManager->getFile($asset));

        $resource = null;
        $size = getimagesize($tempLocation);
        switch ($size['mime']) {
            case 'image/jpeg':
                $resource = imagecreatefromjpeg($tempLocation); //jpeg file
                break;
            case 'image/gif':
                $resource = imagecreatefromgif($tempLocation); //gif file
                break;
            case 'image/png':
                $resource = imagecreatefrompng($tempLocation); //png file
                break;
        }
        $filesystem->remove($tempLocation);

        return $resource;
    }
}
