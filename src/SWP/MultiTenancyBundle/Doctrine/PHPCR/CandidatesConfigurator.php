<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Doctrine\PHPCR;

class CandidatesConfigurator
{
    private $pathBuilder;

    public function __construct($pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    public function configure($candidates)
    {
        $candidates->setPrefixes(
            $this->pathBuilder->build(['routes', 'content'])
        );
    }

    // ...
}
