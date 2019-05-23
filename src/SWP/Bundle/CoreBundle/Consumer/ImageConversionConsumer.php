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
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolverInterface;
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

    protected $assetLocationResolver;

    public function __construct(
        SerializerInterface $serializer,
        LoggerInterface $logger,
        MediaManagerInterface $mediaManager,
        TenantContextInterface $tenantContext,
        EntityManagerInterface $entityManager,
        AssetLocationResolverInterface $assetLocationResolver
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->mediaManager = $mediaManager;
        $this->tenantContext = $tenantContext;
        $this->entityManager = $entityManager;
        $this->assetLocationResolver = $assetLocationResolver;
    }

    public function execute(AMQPMessage $message): int
    {
        try {
            $decodedMessage = unserialize($message->body, [ImageRenditionInterface::class, TenantInterface::class]);
            /** @var ImageRenditionInterface $imageRendition */
            $imageRendition = $decodedMessage['rendition'];
            /** @var TenantInterface $imageRendition */
            $tenant = $decodedMessage['tenant'];

            $this->tenantContext->setTenant($this->entityManager->find(Tenant::class, $tenant->getId()));
        } catch (RuntimeException $e) {
            $this->logger->error('Message REJECTED: '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        }

        $mediaId = $imageRendition->getImage()->getAssetId();
        $tempLocation = rtrim(sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.sha1($mediaId);

        try {
            imagewebp($this->getImageAsResource($imageRendition->getImage()), $tempLocation);
            $uploadedFile = new UploadedFile($tempLocation, $mediaId, 'image/webp', strlen($tempLocation), null, true);
            $this->mediaManager->saveFile($uploadedFile, $mediaId);

            $this->logger->info(sprintf('File "%s" converted successfully to WEBP', $mediaId));
        } catch (Exception $e) {
            $this->logger->error('File NOT converted '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);
        } finally {
            $filesystem = new Filesystem();
            if ($filesystem->exists($tempLocation)) {
                $filesystem->remove($tempLocation);
            }
        }

        $imageRendition = $this->entityManager->find(ImageRendition::class, $imageRendition->getId());
        $imageRendition->getImage()->addVariant(ImageInterface::VARIANT_WEBP);
        $this->entityManager->flush();

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
