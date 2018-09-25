<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Model\KeywordInterface;

class KeywordFactory implements KeywordFactoryInterface
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

    public function create(string $name, string $slug = null): KeywordInterface
    {
        return new $this->className($name, $slug);
    }
}
