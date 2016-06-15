<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Bridge\Transformer;

use SWP\Component\Bridge\Exception\TransformationFailedException;

interface DataTransformerInterface
{
    /**
     * Transforms value.
     *
     * @param mixed $value
     
     * @return mixed
     *
     * @throws TransformationFailedException
     */
    public function transform($value);

    /**
     * Transforms value in reverse mode.
     *
     * @param mixed $value
     
     * @return mixed
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($value);
}
