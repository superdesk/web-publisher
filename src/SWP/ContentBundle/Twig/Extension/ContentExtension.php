<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\ContentBundle\Twig\Extension;

use SWP\ContentBundle\Document\Article;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Router;
use SWP\ContentBundle\Model\Page;

class ContentExtension extends \Twig_Extension
{
    /**
     * Entity Manager.
     *
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;

    /**
     * Router.
     *
     * @var Router
     */
    protected $router;

    public function __construct(Registry $doctrine, Router $router)
    {
        $this->om = $doctrine->getManager();
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            'gimmeUrl' => new \Twig_SimpleFunction('gimmeUrl', array($this, 'gimmeUrl')),
            new \Twig_SimpleFunction('generateUrlFor*', function ($name, $object) {
                if ($name == 'Article') {
                    if ($object->getValues() instanceof Article) {
                        $pageArticle = $this->em->getRepository('SWP\ContentBundle\Model\PageContent')
                            ->getForContentPath($object->getValues()->getId())
                            ->getOneOrNullResult();
                        $page = $pageArticle->getPage();

                        if ($page->getType() === Page::PAGE_TYPE_CONTENT) {
                            return $this->router->generate($page->getRouteName(), []);
                        } elseif ($page->getType() === Page::PAGE_TYPE_CONTAINER) {
                            return $this->router->generate($page->getRouteName(), [
                                'contentSlug' => $object->getValues()->getSlug(),
                            ]);
                        }
                    }
                }

                return false;
            }),
        );
    }

    public function getName()
    {
        return 'swp_content';
    }

    public function gimmeUrl($object)
    {
        if (is_object($object) && method_exists($object, 'getValues')) {
            if ($object->getValues() instanceof Article) {
                $pageArticle = $this->om->getRepository('SWP\ContentBundle\Model\PageContent')
                    ->getByContentPath($object->getValues()->getId())
                    ->getOneOrNullResult();

                if ($pageArticle) {
                    $page = $pageArticle->getPage();

                    return $this->router->generate($page->getRouteName(), [
                        'contentSlug' => $object->getValues()->getSlug(),
                    ]);
                }
            }
        }

        return false;
    }
}
