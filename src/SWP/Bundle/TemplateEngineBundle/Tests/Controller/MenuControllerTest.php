<?php
/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class MenuControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml'
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->router = $this->getContainer()->get('router');
    }


    public function testFixture()
    {
        /** @var DocumentManager $dm */
        $dm = $this->getContainer()->get('document_manager');
        $mp = $this->getContainer()->get('swp_template_engine.menu_provider');
        $path = $mp->getMenuRoot();

        $menuParent = $dm->find(null, $path);

        $menu = new Menu();
        $menu->setName('main-menu');
        $menu->setLabel('Main menu');
        $menu->setLocale('en');
        $menu->setParentDocument($menuParent);

        $dm->persist($menu);

        $home = new MenuNode();
        $home->setName('home');
        $home->setLabel('Home');
        $home->setParentDocument($menu);
        $home->setLocale('en');
        $home->setUri('http://www.example.com/home');

        $dm->persist($home);

        $contact = new MenuNode();
        $contact->setName('contact');
        $contact->setLabel('Contact');
        $contact->setParentDocument($menu);
        $contact->setLocale('en');
        $contact->setUri('http://www.example.com/contact');

        $dm->persist($contact);

        $subContact = new MenuNode();
        $subContact->setName('sub-contact');
        $subContact->setLabel('Subcontact');
        $subContact->setParentDocument($contact);
        $subContact->setLocale('en');
        $subContact->setUri('http://www.example.com/sub/contact');

        $dm->persist($subContact);
        $dm->flush();

        $menus = $dm->getChildren($menuParent);
        $menus = $menus->toArray();

        $menu = $dm->find('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode', '/cms/menu/main-menu/contact');

        /** @var QueryBuilder $qb */
        $qb = $dm->createQueryBuilder();
        $qb->from()->document('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode', 'm');
        $qb->where()->descendant('/cms/menu/main-menu', 'm');
        //$qb->where()->eq()->field('m.label')->literal('Subcontact');
        $query = $qb->getQuery();

        $subs = $query->getResult();
        $subs = $subs->toArray();

        $hopeful = $dm->find(null, '/cms/menu/main-menu');
        $hopeless = $dm->find(null, '/cms/menu/main-menu/contact');
    }
}
