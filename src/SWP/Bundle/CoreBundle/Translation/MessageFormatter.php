<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Translation;

use function strpos;
use function substr;
use Symfony\Component\Translation\Formatter\MessageFormatter as BaseMessageFormatter;

class MessageFormatter extends BaseMessageFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format($message, $locale, array $parameters = array()): string
    {
        $position = strpos($locale, '@');
        if (false !== $position) {
            $locale = substr($locale, 0, $position);
        }

        return parent::format($message, $locale, $parameters);
    }
}
