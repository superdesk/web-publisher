<?php

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

interface ArticleFactoryInterface extends FactoryInterface
{
    /**
     * Creates a new Article object from Package.
     * 
     * @param PackageInterface $package
     *
     * @return mixed
     */
    public function createFromPackage(PackageInterface $package);
}
