<?php

namespace SWP\Bundle\ContentBundle\DependencyInjection\Driver\Exception;

class InvalidDriverException extends \InvalidArgumentException
{
    public function __construct($driver)
    {
        parent::__construct(sprintf('%s is not valid driver', $driver));
    }
}
