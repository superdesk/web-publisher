<?php

namespace SWP\Bundle\ContentBundle\Model;

abstract class AbstractManager
{
    public function createNew()
    {
        $class = $this->getObjectClass();

        return new $class();
    }

    abstract public function getObjectClass();
}
