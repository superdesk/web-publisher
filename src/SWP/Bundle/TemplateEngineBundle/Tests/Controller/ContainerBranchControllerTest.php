<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ContainerBranchControllerTest extends WebTestCase
{
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/Container.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/WidgetModel.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateContainerBranchApi()
    {
        $containerService = $this->getContainer()->get('swp_template_engine_container');
        $container = $containerService->createNewContainer('testicicle', ['data' => ['jiminy' => 'cricket']]);

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_container_branch'), [
            'containerBranch' => [
                'source' => $container->getId(),
                'target_name' => 0,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
