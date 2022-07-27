<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class ArticleUnpublishFromThemeGeneratedDataTest extends WebTestCase
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
        parent::setUp();

        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);

        $this->router = $this->getContainer()->get('router');

        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->get('swp_core.uploader.theme')->getAvailableThemesPath());
        $filesystem->remove($this->getContainer()->getParameter('kernel.project_dir').'/web/bundles/_themes/swp/test-theme@123abc');
    }

    public function testUnpublishArticleFromThemeGeneratedData()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant for theme installation',
                'subdomain' => 'newtheme',
                'domainName' => 'localhost',
                'organization' => '123456',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $newTenant = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient([], [
            'HTTP_HOST' => 'newtheme.localhost',
        ]);

        $client->request('GET', $this->router->generate('swp_api_list_available_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);

        $filesystem = new Filesystem();
        $tempThemeDir = $this->getContainer()->getParameter('kernel.cache_dir').'/temp_theme/';
        $filesystem->mkdir($tempThemeDir);
        $filesystem->mirror(realpath(__DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install_with_generated_data/'), $tempThemeDir.'/test_theme', null, ['override' => true, 'delete' => true]);

        $fileName = $this->createZipArchive($tempThemeDir);
        $client->request('POST', $this->router->generate('swp_api_upload_theme'), [],[
                'file' => new UploadedFile($fileName, 'test_theme.zip', 'application/zip', null, true),
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $filesystem->remove($fileName);

        $client->request('GET', $this->router->generate('swp_api_list_tenant_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $data['_embedded']['_items']);

        $client->request('POST', $this->router->generate('swp_api_install_theme'), [
                'name' => 'swp/test-theme-install-generated-data',
                'processGeneratedData' => true,
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_tenant_themes'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $data['_embedded']['_items']);
        self::assertEquals('swp/test-theme-install-generated-data@'.$newTenant['code'], $data['_embedded']['_items'][0]['name']);

        $filesystem->remove(realpath(__DIR__.'/../Fixtures/themes/'.$newTenant['code'].'/'));

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('published', $content['status']);

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 1]), [
                'status' => 'unpublished',
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('unpublished', $content['status']);
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
