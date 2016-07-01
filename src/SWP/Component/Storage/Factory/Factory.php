<?php

namespace SWP\Component\Storage\Factory;

class Factory implements FactoryInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * Factory constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new $this->className();
    }
}
