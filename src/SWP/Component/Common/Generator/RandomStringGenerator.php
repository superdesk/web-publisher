<?php

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Generator;

class RandomStringGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    protected $chars = [];

    public function __construct()
    {
        $this->chars = array_merge(range(0, 9), range('a', 'z'));
    }

    public function generate(int $length): string
    {
        if (empty($length)) {
            throw new \InvalidArgumentException("Length can't be empty.");
        }

        $random = [];
        $charsCount = count($this->chars);
        for ($i = 0; $i < $length; ++$i) {
            $random[] = $this->chars[mt_rand(0, $charsCount - 1)];
        }

        return implode('', $random);
    }
}
