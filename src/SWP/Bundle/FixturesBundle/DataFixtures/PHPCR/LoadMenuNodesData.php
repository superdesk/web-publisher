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
use SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadWidgetsData;
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
        if ('test' === $env) {
            $menuNodes = [
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
                    'parent' => $defaultTenantPrefix . '/menu/test/contact',
                ],
            ];

            $defaultParent = $manager->find(null, $defaultTenantPrefix . '/menu/test');
            foreach ($menuNodes as $menuNodeData) {
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
        } else {
            $parent =  $manager->find(null, $defaultTenantPrefix.'/menu/'.LoadWidgetsData::MAIN_NAV_MENU_NAME);
            foreach (LoadRoutesData::DEV_ROUTES as $routeName) {
                $menuNode = new MenuNode();
                $menuNode->setName($routeName);
                $menuNode->setLabel($routeName);
                $menuNode->setRoute($routeName);
                $menuNode->setLocale('en');
                $menuNode->setParentDocument($parent);
            }
        }
    }

    public function getOrder()
    {
        return 5;
    }
}
