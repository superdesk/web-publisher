<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ClearableCache;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use SWP\Bundle\FixturesBundle\Registry\FixtureRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class WebTestCase extends BaseWebTestCase {
  protected ?AbstractDatabaseTool $databaseTool;
  private static ?KernelBrowser $client = null;

  public function setUp(): void {
    if (self::$client == null) {
      self::$client = parent::createClient();
    }
    $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
  }

  /**
   * Initializes database.
   */
  protected function initDatabase() {
    $this->clearMetadataCache();
  }

  /**
   * Clears metadata cache of the various cache drivers.
   */
  private function clearMetadataCache() {
    $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
    /** @var ClearableCache $cacheDriver */
    $cacheDriver = $entityManager->getConfiguration()->getMetadataCacheImpl();

    if (!$cacheDriver instanceof ArrayCache) {
      $cacheDriver->deleteAll();
    }
  }

  protected function tearDown(): void {
    unset($this->databaseTool);
    $reflection = new \ReflectionObject($this);
    foreach ($reflection->getProperties() as $prop) {
      if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
        $prop->setAccessible(true);
        if ($prop->name == 'databaseTool') {
          continue;
        }
        $prop->setValue($this, null);
      }
    }
    parent::tearDown();
  }

  protected function loadCustomFixtures(array $fixtures) {
    $env = $this->getContainer()->getParameter('test_env');

    $registry = new FixtureRegistry();
    $registry->setEnvironment($env);

    return $this->databaseTool->loadFixtures($registry->getFixtures($fixtures))->getReferenceRepository();
  }

  public static function createClient(array $options = [], array $server = []) {
    if (!array_key_exists('HTTP_Authorization', $server)) {
      $server['HTTP_Authorization'] = base64_encode('test_token:');
    }

    if (null === $server['HTTP_Authorization']) {
      unset($server['HTTP_Authorization']);
    }

    if(!array_key_exists("HTTP_HOST", $server)) {
      $server['HTTP_HOST'] = 'localhost';
    }

    $newClient = clone self::$client;
    $newClient->setServerParameters($server);
    return $newClient;
  }
}
