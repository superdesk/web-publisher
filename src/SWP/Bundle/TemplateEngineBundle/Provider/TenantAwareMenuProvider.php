<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplateEngineBundle\Provider;

use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Menu\Loader\NodeLoader;

class TenantAwareMenuProvider extends PhpcrMenuProvider
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    protected $pathBuilder;

    public function __construct(
        NodeLoader $loader,
        ManagerRegistry $managerRegistry,
        TenantAwarePathBuilderInterface $pathBuilder,
        $basePath)
    {
        $this->pathBuilder = $pathBuilder;
        $this->basePath = $basePath;
        parent::__construct($loader, $managerRegistry, null);
    }

    /**
     * @return string
     *
     * {@inheritdoc}
     */
    public function getMenuRoot()
    {
        if (null === $this->menuRoot) {
            $this->menuRoot = $this->pathBuilder->build($this->basePath);
        }

        return parent::getMenuRoot();
    }

    public function getMenuParent()
    {
        return $this->getObjectManager()->find(null, $this->getMenuRoot());
    }

    public function getMenu($id)
    {
        $id = $this->getMenuRoot().'/'.$id;

        return $this->getObjectManager()->find('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu', $id);
    }

    public function getMenuNode($menuId, $nodeId)
    {
        $id = $this->getMenuRoot().'/'.$menuId.'/'.$nodeId;

        return $this->getObjectManager()->find('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode', $id);
    }

    public function getMenuNodeParent($menuId, $nodeId = null)
    {
        if (null === $nodeId) {
            return $this->getMenu($menuId);
        }

        return $this->getMenuNode($menuId, $nodeId);
    }

    public function getAllSubMenus($menuId)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getObjectManager()->createQueryBuilder();
        $qb->from()->document('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode', 'm');
        $path = $this->getMenuRoot().'/'.$menuId;
        $qb->where()->descendant($path, 'm');
        $query = $qb->getQuery();
        $nodes = $query->getResult();

        return $nodes;
    }
}
