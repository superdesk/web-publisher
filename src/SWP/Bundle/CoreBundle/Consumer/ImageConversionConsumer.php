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
use function imagewebp;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\CoreBundle\Model\ImageInterface;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageConversionConsumer implements ConsumerInterface
{
    protected $serializer;

    protected $logger;

    protected $mediaManager;

    protected $tenantContext;

    protected $entityManager;

    public function __construct(
        SerializerInterface $serializer,
        LoggerInterface $logger,
        MediaManagerInterface $mediaManager,
        TenantContextInterface $tenantContext,
        EntityManagerInterface $entityManager
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->mediaManager = $mediaManager;
        $this->tenantContext = $tenantContext;
        $this->entityManager = $entityManager;
    }

    public function execute(AMQPMessage $message): int
    {
        try {
            ['renditionId' => $imageRenditionId, 'tenantId' => $tenantId] = unserialize($message->body, [false]);
            if (($tenant = $this->entityManager->find(Tenant::class, $tenantId)) instanceof TenantInterface) {
                $this->tenantContext->setTenant($tenant);
            }
        } catch (RuntimeException $e) {
            $this->logger->error('Message REJECTED: '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        }

        $imageRendition = $this->entityManager->find(ImageRendition::class, $imageRenditionId);
        if (null !== $imageRendition) {
            $mediaId = $imageRendition->getImage()->getAssetId();
            $tempLocation = rtrim(sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.sha1($mediaId);

            try {
                if (!function_exists('imagewebp')) {
                    throw new BadFunctionCallException('"imagewebp" function is missing. Looks like GD was compiled without webp support');
                }
                imagewebp($this->getImageAsResource($imageRendition->getImage()), $tempLocation);
                $uploadedFile = new UploadedFile($tempLocation, $mediaId, 'image/webp', strlen($tempLocation), null, true);
                $this->mediaManager->saveFile($uploadedFile, $mediaId);

                $this->logger->info(sprintf('File "%s" converted successfully to WEBP', $mediaId));

                $imageRendition->getImage()->addVariant(ImageInterface::VARIANT_WEBP);
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
        }

        return ConsumerInterface::MSG_ACK;
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
