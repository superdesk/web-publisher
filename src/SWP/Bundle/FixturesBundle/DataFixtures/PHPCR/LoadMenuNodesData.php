<?php
/**
 * Created by PhpStorm.
 * User: sourcefabric
 * Date: 14/07/16
 * Time: 13:45.
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class LoadMenuNodesData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();
        $menuNodes = [
            'dev' => [

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
                    'parent' => '/swp/default/menu/test/contact',
                ],
            ],
        ];

        if (isset($menuNodes[$env])) {
            $defaultParent = $manager->find(null, 'swp/default/menu/test');
            foreach ($menuNodes[$env] as $menuNodeData) {
                $menuNode = new MenuNode();
                $menuNode->setName($menuNodeData['name']);
                $menuNode->setLabel($menuNodeData['label']);
                $menuNode->setLocale($menuNodeData['locale']);
                $menuNode->setUri($menuNodeData['uri']);
                $parent = isset($menuNodeData['parent']) ? $manager->find(null, $menuNodeData['parent']) : $defaultParent;
                $menuNode->setParentDocument($parent);
                $manager->persist($menuNode);
                $manager->flush();
            }
        }
    }
}
