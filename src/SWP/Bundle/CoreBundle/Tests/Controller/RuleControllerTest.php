<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class RuleControllerTest extends WebTestCase
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
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
        ], null, 'doctrine_phpcr');

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/Rule.yml',
        ], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testListRulesApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_rule'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/rules\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/rules\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/rules\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":1,"configuration":{"route":"articles\/features"},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}]}}';

        self::assertEquals($expected, $data);
    }

    public function testGetSingleRuleApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_get_rule', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":1,"configuration":{"route":"articles\/features"},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testUpdateSingleRuleApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_rule', [
            'id' => 1,
        ]), [
            'rule' => [
                'priority' => 22,
                'configuration' => [
                    [
                        'key' => 'templateName',
                        'value' => 'test.html.twig',
                    ],
                ],
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":22,"configuration":{"templateName":"test.html.twig"},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testUpdateSingleRuleWithMoreConfigurationApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_rule', [
            'id' => 1,
        ]), [
            'rule' => [
                'priority' => 22,
                'configuration' => [
                    [
                        'key' => 'templateName',
                        'value' => 'test.html.twig',
                    ],
                    [
                        'key' => 'route',
                        'value' => 'articles/features',
                    ],
                ],
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":22,"configuration":{"templateName":"test.html.twig","route":"articles\/features"},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testDeleteSingleRuleApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_core_delete_rule', ['id' => 1]));

        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testCreateNewRuleApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Sydney/"',
                'priority' => 2,
                'configuration' => [
                    [
                        'key' => 'templateName',
                        'value' => 'sydney.html.twig',
                    ],
                    [
                        'key' => 'route',
                        'value' => 'articles/get-involved',
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":2,"expression":"article.getMetadataByKey(\"located\") matches \"\/Sydney\/\"","priority":2,"configuration":{"templateName":"sydney.html.twig","route":"articles\/get-involved"},"_links":{"self":{"href":"\/api\/v1\/rules\/2"}}}';

        self::assertEquals($expected, $data);
    }
}
