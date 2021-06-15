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

namespace SWP\Bundle\CoreBundle\Tests\Twig;

use SWP\Bundle\CoreBundle\Twig\Cache\TenantAwareCache;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class TenantAwareCacheTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $cachePath;

    public function setUp(): void
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant']);

        $this->twig = new \Twig_Environment(new \Twig_Loader_Array(), array('debug' => true, 'cache' => false));
        $this->cachePath = __DIR__.'/../Functional/Resources/cache';
        $tenantAwareCache = new TenantAwareCache(
            $this->cachePath.'/twig',
            $this->getContainer()->get('swp_multi_tenancy.tenant_context')
        );
        $this->twig->setCache($tenantAwareCache);
    }

    public function testCreateCacheAfterRendering()
    {
        $filesystem = new Filesystem();
        self::assertFalse($filesystem->exists($this->cachePath.'/twig/123abc/themes/swp_test-theme'));
        self::assertEquals($this->getRendered('aa bb ccc'), 'aa bb ccc');
        self::assertTrue(file_exists($this->cachePath.'/twig/123abc/themes/swp_test-theme'));
        $filesystem->remove($this->cachePath);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
