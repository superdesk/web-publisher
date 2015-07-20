<?php

namespace SWP\TemplateEngineBundle\Annotation;

use Doctrine\Common\Annotations\Reader;
use SWP\TemplateEngineBundle\Annotation\Context;

class ContextReader
{
    private $reader;

    private $annotationClass = 'SWP\\TemplateEngineBundle\\Annotation\\Context';

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Read Context annotation from controller
     *
     * @param object $originalObject Array with controller object and method name
     *
     * @return Context
     */
    public function read($originalObject)
    {
        $reflectionMethod = new \ReflectionMethod($originalObject[0], $originalObject[1]);

        return $this->reader->getMethodAnnotation($reflectionMethod, $this->annotationClass);
    }
}
