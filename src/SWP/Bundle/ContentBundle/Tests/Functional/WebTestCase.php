<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use SWP\Bundle\ContentBundle\Tests\Functional\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class WebTestCase extends BaseWebTestCase {
  protected ?AbstractDatabaseTool $databaseTool;
  private static ?KernelBrowser $client = null;

  protected $manager;

  public function setUp(): void {
    if (self::$client == null) {
      self::$client = parent::createClient();
    }
    $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
  }

  protected static function getKernelClass(): string {
    require_once __DIR__ . '/app/AppKernel.php';

    return AppKernel::class;
  }

  protected function tearDown(): void {
    parent::tearDown();
    unset($this->databaseTool);
  }


  protected function initDatabase(): void {
    $kernel = $this->createKernel();
    $kernel->boot();
    $this->manager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    $schemaTool = new SchemaTool($this->manager);
    $metadata = $this->manager->getMetadataFactory()->getAllMetadata();
    $schemaTool->dropSchema($metadata);
    $schemaTool->createSchema($metadata);
  }

  public static function createClient(array $options = [], array $server = []) {
    $newClient = clone self::$client;
    $newClient->setServerParameters($server);
    $newClient->getKernel()->shutdown();
    $newClient->getKernel()->boot();
    return $newClient;
  }
}
