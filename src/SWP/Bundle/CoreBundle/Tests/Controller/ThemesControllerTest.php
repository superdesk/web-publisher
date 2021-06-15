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

class ThemesControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);

        $this->router = $this->getContainer()->get('router');

        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->get('swp_core.uploader.theme')->getAvailableThemesPath());
        $filesystem->remove($this->getContainer()->getParameter('sylius_core.public_dir').'/bundles/_themes/swp/test-theme@123abc');
    }

    public function testThemeUpload()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_list_available_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);

        $fileName = $this->createZipArchive(realpath(__DIR__.'/../Fixtures/themes/123abc/'));
        $client->request('POST', $this->router->generate('swp_api_upload_theme'), [
                'file' => new UploadedFile($fileName, 'test_theme.zip', 'application/zip', filesize($fileName), null, true),
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_available_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);

        $filesystem = new Filesystem();
        $filesystem->remove($fileName);
    }

    public function testAddingUrlToThemeScreenshots()
    {
        $client = static::createClient();
        $fileName = $this->createZipArchive(realpath(__DIR__.'/../Fixtures/themes/123abc/'));
        $client->request('POST', $this->router->generate('swp_api_upload_theme'), [
                'file' => new UploadedFile($fileName, 'test_theme.zip', 'application/zip', filesize($fileName), null, true),
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_available_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);
        self::assertCount(1, $data['_embedded']['_items'][0]['screenshots']);
        self::assertArrayHasKey('url', $data['_embedded']['_items'][0]['screenshots'][0]);

        $filesystem = new Filesystem();
        $filesystem->remove($fileName);
    }

    public function testThemeInstall()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_list_available_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);

        $fileName = $this->createZipArchive(realpath(__DIR__.'/../Fixtures/themes/123abc/'));
        $client->request('POST', $this->router->generate('swp_api_upload_theme'), [
                'file' => new UploadedFile($fileName, 'test_theme.zip', 'application/zip', filesize($fileName), null, true),
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $filesystem = new Filesystem();
        $filesystem->remove($fileName);
        $filesystem->remove(realpath(__DIR__.'/../Fixtures/themes/123abc/'));

        $client->request('GET', $this->router->generate('swp_api_list_tenant_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);

        $asset = $this->getContainer()->getParameter('sylius_core.public_dir').'/bundles/_themes/swp/test-theme@123abc/css/test.css';
        self::assertFileNotExists($asset);

        $client->request('POST', $this->router->generate('swp_api_install_theme'), [
            'name' => 'swp/test-theme',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_tenant_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);
        self::assertEquals('swp/test-theme@123abc', $data['_embedded']['_items'][0]['name']);

        $asset = $this->getContainer()->getParameter('sylius_core.public_dir').'/bundles/_themes/swp/test-theme@123abc/css/test.css';
        self::assertFileExists($asset);
        $filesystem->remove($this->getContainer()->getParameter('sylius_core.public_dir').'/bundles/_themes/swp/test-theme@123abc');
    }

    public function testTenantCreationThemeUploadAndInstallationWithActivation()
    {
        $defaultClient = static::createClient();
        $defaultClient->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant for theme installation',
                'subdomain' => 'newtheme',
                'domainName' => 'localhost',
                'organization' => '123456',
        ]);

        $this->assertEquals(201, $defaultClient->getResponse()->getStatusCode());
        $newTenant = json_decode($defaultClient->getResponse()->getContent(), true);

        $client = static::createClient([], [
            'HTTP_HOST' => 'newtheme.localhost',
        ]);

        $client->request('GET', $this->router->generate('swp_api_list_available_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);

        $filesystem = new Filesystem();
        $tempThemeDir = $this->getContainer()->getParameter('kernel.cache_dir').'/temp_theme/';
        $filesystem->mkdir($tempThemeDir);
        $filesystem->mirror(realpath(__DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install_with_generated_data/'), $tempThemeDir.'/test_theme', null, ['override' => true, 'delete' => true]);

        $fileName = $this->createZipArchive($tempThemeDir);
        $client->request('POST', $this->router->generate('swp_api_upload_theme'), [
                'file' => new UploadedFile($fileName, 'test_theme.zip', 'application/zip', filesize($fileName), null, true),
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $filesystem->remove($fileName);

        $client->request('GET', $this->router->generate('swp_api_list_tenant_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);

        $client->request('GET', $this->router->generate('swp_api_content_list_articles'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $data['_embedded']['_items']);
        $client->request('POST', $this->router->generate('swp_api_install_theme'), [
            'name' => 'swp/test-theme-install-generated-data', 'processGeneratedData' => true,
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('GET', $this->router->generate('swp_api_content_list_articles'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);

        $defaultClient->request('GET', $this->router->generate('swp_api_content_list_routes'));
        self::assertEquals(200, $defaultClient->getResponse()->getStatusCode());
        $data = json_decode($defaultClient->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);
        $defaultClient->request('POST', $this->router->generate('swp_api_install_theme'), [
            'name' => 'swp/test-theme-install-generated-data',
        ]);
        self::assertEquals(201, $defaultClient->getResponse()->getStatusCode());
        $defaultClient->request('GET', $this->router->generate('swp_api_content_list_routes'));
        self::assertEquals(200, $defaultClient->getResponse()->getStatusCode());
        $data = json_decode($defaultClient->getResponse()->getContent(), true);
        self::assertCount(4, $data['_embedded']['_items']);

        $client->request('GET', $this->router->generate('swp_api_list_tenant_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);
        self::assertEquals('swp/test-theme-install-generated-data@'.$newTenant['code'], $data['_embedded']['_items'][0]['name']);

        $filesystem->remove(realpath(__DIR__.'/../Fixtures/themes/'.$newTenant['code'].'/'));
        $filesystem->remove(realpath(__DIR__.'/../Fixtures/themes/123abc/swp__test-theme-install-generated-data/'));
    }

    private function createZipArchive($rootPath)
    {
        $zip = new \ZipArchive();
        $zipName = $this->getContainer()->getParameter('kernel.cache_dir').'/'.time().'.zip';
        $zip->open($zipName, \ZipArchive::CREATE);

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
