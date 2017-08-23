<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class ThemeControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);

        $this->router = $this->getContainer()->get('router');
    }

    public function testThemeUpload()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_list_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);

        $fileName = $this->createZipArchive();
        $client->request('POST', $this->router->generate('swp_api_upload_theme'), [
            'theme_upload' => [
                'file' => new UploadedFile($fileName, 'test_theme.png', 'application/zip', filesize($fileName), null, true),
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);

        $filesystem = new Filesystem();
        $filesystem->remove($fileName);
        $filesystem->remove($this->getContainer()->get('swp_core.uploader.theme')->getAvailableThemesPath());
    }

    private function createZipArchive()
    {
        $zip = new \ZipArchive();
        $zipName = $this->getContainer()->getParameter('kernel.cache_dir').'/'.time().'.zip';
        $zip->open($zipName, \ZipArchive::CREATE);

        $rootPath = realpath(__DIR__.'/../Fixtures/themes/123abc/');
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        return $zipName;
    }
}
