<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

class ComponentTextStyles
{
    /** @var ComponentTextStyle */
    private $default;

    public function getDefault(): ComponentTextStyle
    {
        return $this->default;
    }

    public function setDefault(ComponentTextStyle $default): void
    {
        $this->default = $default;
    }
}
