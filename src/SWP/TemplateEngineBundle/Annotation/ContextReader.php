<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\TemplateEngineBundle\Annotation;

use Doctrine\Common\Annotations\Reader;

class ContextReader
{
    private $reader;

    private $annotationClass = 'SWP\\TemplateEngineBundle\\Annotation\\Context';

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Read Context annotation from controller.
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
