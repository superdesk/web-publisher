<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Manager;

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManagerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->initDatabase();

        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');
    }

    /**
     * Test file upload.
     */
    public function testFileUpload()
    {
        $mediaManager = $this->getContainer()->get('swp_content_bundle.manager.media');

        $media = $mediaManager->handleUploadedFile(
            new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png'),
            'asdgsadfvasdf4w35qwetasftest'
        );

        $this->assertTrue('asdgsadfvasdf4w35qwetasftest' === $media->getAssetId());
        $this->assertTrue('png' === $media->getFileExtension());

        $file = $mediaManager->getFile($media);
        $this->assertTrue($file === file_get_contents(__DIR__.'/../app/Resources/test_file.png'));
    }

    /**
     * Test url generation functions.
     */
    public function testUrlGeneration()
    {
        $mediaManager = $this->getContainer()->get('swp_content_bundle.manager.media');

        $media = $mediaManager->handleUploadedFile(
            new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png'),
            'asdgsadfvasdf4w35qwetasftest'
        );

        $this->assertEquals($mediaManager->getMediaUri($media), '/media/asdgsadfvasdf4w35qwetasftest.png');
        $this->assertEquals($mediaManager->getMediaPublicUrl($media), 'http://localhost/media/asdgsadfvasdf4w35qwetasftest.png');
    }
}
