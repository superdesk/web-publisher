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

    /**
     * @var int
     */
    protected $charsCount;

    /**
     * RandomStringGenerator constructor.
     */
    public function __construct()
    {
        $this->chars = array_merge(range(0, 9), range('a', 'z'));
        $this->charsCount = count($this->chars);
    }

    /**
     * Generate random string.
     *
     * @param int $length
     *
     * @return string
     */
    public function generate($length)
    {
        if (empty($length)) {
            throw new \InvalidArgumentException("Length can't be empty.");
        }

        $random = [];

        for ($i = 0; $i < $length; ++$i) {
            $random[] = $this->chars[mt_rand(0, $this->charsCount - 1)];
        }

        return implode('', $random);
    }
}
