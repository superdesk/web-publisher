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
use SWP\ContentBundle\Model\Page;

class ContentExtension extends \Twig_Extension
{
    protected $em;

    protected $router;

    public function __construct($em, $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            'generateUrlFor*' => new \Twig_SimpleFunction('generateUrlFor*', array($this, 'generateUrlFor')),
        );
    }

    public function getName()
    {
        return 'swp_content';
    }

    public function generateUrlFor($name, $object) {
        if (is_object($object) && method_exists($object, 'getValues')) {
            if ($name == 'Article' && $object->getValues() instanceof Article) {
                $pageArticle = $this->em->getRepository('SWP\ContentBundle\Model\PageContent')
                    ->getForContentPath($object->getValues()->getId())
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
