<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../../../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

require __DIR__.'/AppKernel.php';
