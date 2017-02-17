<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Factory;

use SWP\Component\Common\Generator\RandomStringGenerator;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ContainerFactory implements FactoryInterface
{
    /**
     * @var RandomStringGenerator
     */
    protected $randomStringGenerator;

    /**
     * @var string
     */
    private $className;

    /**
     * Factory constructor.
     *
     * @param string $className
     */
    public function __construct($className, $randomStringGenerator)
    {
        $this->className = $className;
        $this->randomStringGenerator = $randomStringGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        /** @var RevisionAwareInterface $container */
        $container = new $this->className();
        $container->setUuid($this->randomStringGenerator->generate(11));

        return $container;
    }
}
