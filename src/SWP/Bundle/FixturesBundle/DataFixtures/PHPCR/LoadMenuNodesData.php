<?php
/**
 * Created by PhpStorm.
 * User: sourcefabric
 * Date: 14/07/16
 * Time: 13:45.
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class LoadMenuNodesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $defaultTenantPrefix = $this->getTenantPrefix();

        $env = $this->getEnvironment();
        $menuNodes = [
            'dev' => [
                [
                    'name' => 'home',
                    'label' => 'Home',
                    'locale' => 'en',
                    'route' => 'homepage',
                    'parent' => $defaultTenantPrefix.'/menu/default',
                ],
            ],
            'test' => [
                [
                    'name' => 'home',
                    'label' => 'Home',
                    'locale' => 'en',
                    'uri' => 'http://example.com/home',
                ],
                [
                    'name' => 'contact',
                    'label' => 'Contact',
                    'locale' => 'en',
                    'uri' => 'http://example.com/contact',
                ],
                [
                    'name' => 'sub',
                    'label' => 'Sub Contact',
                    'locale' => 'en',
                    'uri' => 'http://example.com/contact/sub',
                    'parent' => $defaultTenantPrefix.'/menu/test/contact',
                ],
            ],
        ];

        if (isset($menuNodes[$env])) {
            $defaultParent = $env === 'test' ? $manager->find(null, $defaultTenantPrefix.'/menu/test') : $manager->find(null, $defaultTenantPrefix.'/menu/default');
            foreach ($menuNodes[$env] as $menuNodeData) {
                $menuNode = new MenuNode();
                $menuNode->setName($menuNodeData['name']);
                $menuNode->setLabel($menuNodeData['label']);
                $menuNode->setLocale($menuNodeData['locale']);
                if (isset($menuNodeData['uri'])) {
                    $menuNode->setUri($menuNodeData['uri']);
                }
                if (isset($menuNodeData['route'])) {
                    $menuNode->setRoute($menuNodeData['route']);
                }
                $parent = isset($menuNodeData['parent']) ? $manager->find(null, $menuNodeData['parent']) : $defaultParent;
                $menuNode->setParentDocument($parent);
                $manager->persist($menuNode);
                $manager->flush();
            }
        }
    }

    public function getOrder()
    {
        return 5;
    }
}
