<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\CoreBundle\Resolver\AssetLocationResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixIncompleteImagesDataCommand extends Command
{
    protected static $defaultName = 'swp:migration:fix-renditions-width-height';

    private $entityManager;

    private $filesystem;

    private $assetLocationResolver;

    public function __construct(
        EntityManagerInterface $entityManager,
        Filesystem $filesystem,
        AssetLocationResolver $assetLocationResolver
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->assetLocationResolver = $assetLocationResolver;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Finds image renditions with width and height set to 0 and sets if from file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $this->entityManager->createQuery('
            SELECT 
                   ir.id, i.assetId, i.fileExtension 
            FROM 
                 SWP\Bundle\ContentBundle\Model\ImageRendition ir
            LEFT JOIN 
                 SWP\Bundle\CoreBundle\Model\Image i WITH ir.image = i.id
            WHERE 
                ir.width = 0
            OR
                ir.height = 0
       ');

        $brokenImages = $query->getResult();
        $counter = 0;
        foreach ($brokenImages as $brokenImage) {
            /** @var ImageRenditionInterface $imageReference */
            $imageReference = $this->entityManager->getReference(ImageRendition::class, $brokenImage['id']);
            $filePath = $this->assetLocationResolver->getMediaBasePath().'/'.$brokenImage['assetId'].'.'.$brokenImage['fileExtension'];

            try {
                $file = $this->filesystem->read($filePath);
                $imageResource = imagecreatefromstring($file);
                $imageReference->setWidth(imagesx($imageResource));
                $imageReference->setHeight(imagesy($imageResource));
                ++$counter;
            } catch (FileNotFoundException $e) {
                continue;
            }

            if ($counter > 4) {
                $output->writeln('<bg=green;options=bold>In progress... Processed '.($counter - 1).' renditions.</>');
                $this->entityManager->flush();
                $this->entityManager->clear();
                $counter = 0;
            }
        }
        $this->entityManager->flush();

        $output->writeln('<bg=green;options=bold>Done. In total processed '.\count($brokenImages).' renditions.</>');

        return 0;
    }
}
